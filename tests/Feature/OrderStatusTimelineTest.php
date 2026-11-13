<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTimelineTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private User $otherCustomer;
    private ShippingZone $zone;
    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@rhs-heritage.test',
            'role'  => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'email' => 'customer@rhs-heritage.test',
            'name'  => 'Pelanggan Asli',
            'role'  => 'customer',
        ]);

        $this->otherCustomer = User::factory()->create([
            'email' => 'other@rhs-heritage.test',
            'name'  => 'Pelanggan Lain',
            'role'  => 'customer',
        ]);

        $this->zone = ShippingZone::create([
            'name'       => 'Jawa Timur',
            'base_rate'  => 12000,
            'is_active'  => true,
        ]);

        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'songkok-tradisi'],
            ['name' => 'Songkok Tradisi', 'sort_order' => 1]
        );

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Songkok Khas RHS',
            'slug'        => 'songkok-khas-rhs-' . uniqid(),
            'base_price'  => 150000,
            'weight_gram' => 500,
            'is_active'   => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'SKU-' . uniqid(),
            'variant_label'  => 'No. 9',
            'attribute_map'  => ['Ukuran' => '9'],
            'price'          => 150000,
            'stock_quantity' => 20,
            'is_active'      => true,
        ]);
    }

    private function createSampleOrder(OrderStatus $status = OrderStatus::MenungguKonfirmasi, ?User $user = null): Order
    {
        $user = $user ?? $this->customer;
        $order = Order::create([
            'user_id'            => $user->id,
            'order_number'       => 'RHS-' . date('Ymd') . '-' . strtoupper(uniqid()),
            'customer_name'      => $user->name,
            'customer_phone'     => '081234567890',
            'address_line'       => 'Jl. Pahlawan No. 10',
            'city'               => 'Surabaya',
            'province'           => 'Jawa Timur',
            'postal_code'        => '60175',
            'shipping_zone_id'   => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'total_weight_gram'  => 500,
            'subtotal'           => 150000,
            'shipping_cost'      => 12000,
            'total'              => 162000,
            'status'             => $status,
            'created_at'         => now()->subDays(3),
        ]);

        OrderItem::create([
            'order_id'           => $order->id,
            'product_variant_id' => $this->variant->id,
            'product_name'       => 'Songkok Khas RHS',
            'variant_label'      => 'No. 9',
            'unit_price'         => 150000,
            'quantity'           => 1,
            'subtotal'           => 150000,
        ]);

        return $order;
    }

    /**
     * Test 1 & 2: Urutan alur timeline selalu tetap:
     * Menunggu Konfirmasi -> Diproses -> Dikirim -> Selesai
     * Untuk pesanan baru: step 1 current, step 2-4 upcoming.
     */
    public function test_timeline_pipeline_order_and_pending_status_states(): void
    {
        $order = $this->createSampleOrder(OrderStatus::MenungguKonfirmasi);
        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'note'        => 'Pesanan berhasil dibuat di sistem.',
            'created_at'  => now()->subDays(3),
        ]);

        $progress = $order->getTimelineProgress();
        $steps = $progress['steps'];

        $this->assertCount(4, $steps);
        $this->assertEquals(OrderStatus::MenungguKonfirmasi->value, $steps[0]['key']);
        $this->assertEquals(OrderStatus::Diproses->value, $steps[1]['key']);
        $this->assertEquals(OrderStatus::Dikirim->value, $steps[2]['key']);
        $this->assertEquals(OrderStatus::Selesai->value, $steps[3]['key']);

        // Step 1: current, has timestamp
        $this->assertEquals('current', $steps[0]['state']);
        $this->assertTrue($steps[0]['is_current']);
        $this->assertNotNull($steps[0]['timestamp']);

        // Step 2, 3, 4: upcoming, no timestamp
        $this->assertEquals('upcoming', $steps[1]['state']);
        $this->assertNull($steps[1]['timestamp']);
        $this->assertEquals('upcoming', $steps[2]['state']);
        $this->assertNull($steps[2]['timestamp']);
        $this->assertEquals('upcoming', $steps[3]['state']);
        $this->assertNull($steps[3]['timestamp']);
    }

    /**
     * Test 3: Pesanan Diproses.
     * Step 1 completed, Step 2 current, Step 3 & 4 upcoming.
     */
    public function test_timeline_for_processing_order(): void
    {
        $order = $this->createSampleOrder(OrderStatus::Diproses);
        $order->update(['confirmed_at' => now()->subDays(2)]);

        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'note'        => 'Pesanan checkout',
            'created_at'  => now()->subDays(3),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::MenungguKonfirmasi->value,
            'to_status'   => OrderStatus::Diproses->value,
            'note'        => 'Dikonfirmasi oleh owner via panel admin',
            'created_at'  => now()->subDays(2),
        ]);

        $progress = $order->fresh()->getTimelineProgress();
        $steps = $progress['steps'];

        $this->assertEquals('completed', $steps[0]['state']);
        $this->assertTrue($steps[0]['is_completed']);
        $this->assertEquals('current', $steps[1]['state']);
        $this->assertTrue($steps[1]['is_current']);
        $this->assertNotNull($steps[1]['timestamp']);

        $this->assertEquals('upcoming', $steps[2]['state']);
        $this->assertNull($steps[2]['timestamp']);
        $this->assertEquals('upcoming', $steps[3]['state']);
        $this->assertNull($steps[3]['timestamp']);
    }

    /**
     * Test 4: Pesanan Dikirim.
     * Step 1 completed, Step 2 completed, Step 3 current, Step 4 upcoming.
     */
    public function test_timeline_for_shipped_order(): void
    {
        $order = $this->createSampleOrder(OrderStatus::Dikirim);
        $order->update([
            'confirmed_at'    => now()->subDays(2),
            'shipping_date'   => now()->subDay(),
            'tracking_number' => 'JT1234567890',
            'courier_name'    => 'J&T Express',
        ]);

        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'created_at'  => now()->subDays(3),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::MenungguKonfirmasi->value,
            'to_status'   => OrderStatus::Diproses->value,
            'created_at'  => now()->subDays(2),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::Diproses->value,
            'to_status'   => OrderStatus::Dikirim->value,
            'note'        => 'Dikirim via J&T Express',
            'created_at'  => now()->subDay(),
        ]);

        $progress = $order->fresh()->getTimelineProgress();
        $steps = $progress['steps'];

        $this->assertEquals('completed', $steps[0]['state']);
        $this->assertEquals('completed', $steps[1]['state']);
        $this->assertEquals('current', $steps[2]['state']);
        $this->assertNotNull($steps[2]['timestamp']);
        $this->assertEquals('upcoming', $steps[3]['state']);
        $this->assertNull($steps[3]['timestamp']);
    }

    /**
     * Test 5: Pesanan Selesai.
     * Seluruh tahapan 1-4 completed dengan bukti timestamp riil.
     */
    public function test_timeline_for_completed_order(): void
    {
        $order = $this->createSampleOrder(OrderStatus::Selesai);
        $order->update([
            'confirmed_at'          => now()->subDays(3),
            'shipping_date'         => now()->subDays(2),
            'tracking_number'       => 'JT9988776655',
            'delivered_at'          => now()->subDay(),
            'delivery_confirmed_by' => $this->customer->id,
        ]);

        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'created_at'  => now()->subDays(4),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::MenungguKonfirmasi->value,
            'to_status'   => OrderStatus::Diproses->value,
            'created_at'  => now()->subDays(3),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::Diproses->value,
            'to_status'   => OrderStatus::Dikirim->value,
            'created_at'  => now()->subDays(2),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::Dikirim->value,
            'to_status'   => OrderStatus::Selesai->value,
            'note'        => 'Pembeli mengonfirmasi penerimaan barang',
            'created_at'  => now()->subDay(),
        ]);

        $progress = $order->fresh()->getTimelineProgress();
        $steps = $progress['steps'];

        $this->assertEquals('completed', $steps[0]['state']);
        $this->assertEquals('completed', $steps[1]['state']);
        $this->assertEquals('completed', $steps[2]['state']);
        $this->assertEquals('completed', $steps[3]['state']);
        $this->assertNotNull($steps[3]['timestamp']);
    }

    /**
     * Test 6: Pesanan Dibatalkan.
     * Cabang Dibatalkan muncul, Selesai TIDAK boleh ditandai selesai.
     */
    public function test_timeline_for_cancelled_order(): void
    {
        $order = $this->createSampleOrder(OrderStatus::Dibatalkan);
        $order->update([
            'cancellation_reason' => 'Salah ukuran songkok oleh pembeli',
        ]);

        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'created_at'  => now()->subDays(2),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::MenungguKonfirmasi->value,
            'to_status'   => OrderStatus::Dibatalkan->value,
            'note'        => 'Dibatalkan: Salah ukuran songkok oleh pembeli',
            'created_at'  => now()->subDay(),
        ]);

        $progress = $order->fresh()->getTimelineProgress();

        $this->assertTrue($progress['is_cancelled']);
        $this->assertNotNull($progress['cancellation']);
        $this->assertEquals('Salah ukuran songkok oleh pembeli', $progress['cancellation']['reason']);

        // Selesai must NOT be reached / completed
        $stepSelesai = $progress['steps'][3];
        $this->assertNotEquals('completed', $stepSelesai['state']);
        $this->assertEquals('cancelled', $stepSelesai['state']);
        $this->assertNull($stepSelesai['timestamp']);
    }

    /**
     * Test 7: Pesanan dengan retur aktif.
     * Retur aktif terdeteksi mandiri tanpa mencemari 4 tahap utama pesanan.
     */
    public function test_active_return_indicator(): void
    {
        $order = $this->createSampleOrder(OrderStatus::Dikirim);
        $order->update([
            'tracking_number' => 'JT11223344',
            'shipping_date'   => now()->subDays(2),
        ]);

        $return = OrderReturn::create([
            'order_id'            => $order->id,
            'user_id'             => $this->customer->id,
            'return_number'       => 'RET-' . date('Ymd') . '-001',
            'reason'              => 'Cacat produksi pada motif sarung',
            'solution'            => 'replacement',
            'description'         => 'Benang terputus di bagian tepi',
            'status'              => ReturnStatus::MenungguReview,
            'created_at'          => now()->subDay(),
        ]);

        $this->assertTrue($order->hasActiveReturn());
        $this->assertNotNull($order->activeReturn());
        $this->assertEquals($return->id, $order->activeReturn()->id);

        // Render customer order detail view and assert return banner is present
        $response = $this->actingAs($this->customer)->get(route('customer.orders.show', $order->order_number));
        $response->assertOk();
        $response->assertSee('Pengajuan Retur Aktif');
        $response->assertSee($return->return_number);
        $response->assertSee('Menunggu Review');
    }

    /**
     * Test 8 & 9: Riwayat diurutkan kronologis (created_at asc, id asc) dan timestamp tidak dipalsukan.
     */
    public function test_chronological_ordering_and_deterministic_tie_breaker(): void
    {
        $order = $this->createSampleOrder(OrderStatus::Diproses);
        $sameTime = now()->subHours(5);

        // Simpan 2 log dengan timestamp yang persis sama
        $log1 = OrderStatusLog::create([
            'order_id'    => $order->id,
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'note'        => 'Log pertama',
            'created_at'  => $sameTime,
        ]);
        $log2 = OrderStatusLog::create([
            'order_id'    => $order->id,
            'from_status' => OrderStatus::MenungguKonfirmasi->value,
            'to_status'   => OrderStatus::Diproses->value,
            'note'        => 'Log kedua',
            'created_at'  => $sameTime,
        ]);

        $logs = $order->fresh()->statusLogs;

        $this->assertEquals($log1->id, $logs[0]->id);
        $this->assertEquals($log2->id, $logs[1]->id);
        $this->assertEquals('Log pertama', $logs[0]->note);
        $this->assertEquals('Log kedua', $logs[1]->note);
    }

    /**
     * Test 10: Keamanan - Customer HANYA boleh melihat pesanan miliknya sendiri.
     */
    public function test_customer_cannot_view_other_customers_order(): void
    {
        $order = $this->createSampleOrder(OrderStatus::MenungguKonfirmasi, $this->customer);

        // Akses oleh customer lain harus 403 Forbidden
        $response = $this->actingAs($this->otherCustomer)->get(route('customer.orders.show', $order->order_number));
        $response->assertForbidden();
    }

    /**
     * Test 11: Admin melihat detail & riwayat pesanan dengan benar.
     */
    public function test_admin_can_view_order_detail_and_timeline(): void
    {
        $order = $this->createSampleOrder(OrderStatus::MenungguKonfirmasi, $this->customer);
        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'note'        => 'Pesanan diterima dari sistem.',
            'created_at'  => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.show', $order));
        $response->assertOk();
        $response->assertSee('Alur & Progres Pesanan', false);
        $response->assertSee('Riwayat Aktivitas & Log Status', false);
        $response->assertSee('Menunggu Konfirmasi');
    }

    /**
     * Test 12: Public tracking page menampilkan timeline & riwayat secara valid.
     */
    public function test_public_order_tracking_page_renders_timeline(): void
    {
        $order = $this->createSampleOrder(OrderStatus::MenungguKonfirmasi, $this->customer);
        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'note'        => 'Pesanan masuk via checkout web.',
            'created_at'  => now(),
        ]);

        $response = $this->post(route('order.track.submit'), [
            'order_number'   => $order->order_number,
            'customer_phone' => $order->customer_phone,
        ]);

        $response->assertOk();
        $response->assertSee('Alur & Progres Pesanan', false);
        $response->assertSee('Menunggu Konfirmasi');
        $response->assertSee('Pesanan masuk via checkout web.');
    }

    /**
     * Test 13: Penanganan string maupun enum instance bebas error.
     */
    public function test_enum_and_string_status_compatibility(): void
    {
        $this->assertEquals('Menunggu Konfirmasi', OrderStatus::labelFor('menunggu_konfirmasi'));
        $this->assertEquals('Diproses', OrderStatus::labelFor(OrderStatus::Diproses));
        $this->assertEquals('Dikirim', OrderStatus::labelFor('dikirim'));
        $this->assertEquals('Selesai', OrderStatus::labelFor(OrderStatus::Selesai));
        $this->assertEquals('Dibatalkan', OrderStatus::labelFor('dibatalkan'));
        $this->assertEquals('-', OrderStatus::labelFor(''));
    }

    /**
     * Test 14 & 15: Dukungan UI Responsive (Desktop/Mobile) dan Dark Mode.
     */
    public function test_view_contains_responsive_and_dark_mode_tokens(): void
    {
        $order = $this->createSampleOrder(OrderStatus::MenungguKonfirmasi, $this->customer);
        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'created_at'  => now(),
        ]);

        $viewHtml = view('components.order.progress-timeline', [
            'order'   => $order,
            'context' => 'customer',
        ])->render();

        // Desktop and Mobile containers
        $this->assertStringContainsString('hidden md:flex', $viewHtml);
        $this->assertStringContainsString('md:hidden', $viewHtml);

        // Dark mode classes
        $this->assertStringContainsString('dark:bg-[#15221d]', $viewHtml);
        $this->assertStringContainsString('dark:text-white', $viewHtml);
    }

    /**
     * Test 16a: Deteksi inkonsistensi jika tahapan dilewati secara tidak sah.
     */
    public function test_inconsistent_skipped_step_is_detected(): void
    {
        // Order langsung diset ke Selesai tanpa log Diproses / Dikirim
        $order = $this->createSampleOrder(OrderStatus::Selesai, $this->customer);

        $progress = $order->getTimelineProgress();

        $this->assertTrue($progress['has_inconsistency']);
        $this->assertEquals('skipped', $progress['steps'][1]['state']);
        $this->assertEquals('skipped', $progress['steps'][2]['state']);
    }

    /**
     * Test 16b: Admin dilarang menyelesaikan pesanan normal secara langsung.
     */
    public function test_admin_cannot_directly_complete_normal_order(): void
    {
        $order = $this->createSampleOrder(OrderStatus::Dikirim, $this->customer);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.status', $order), [
            'target_status' => OrderStatus::Selesai->value,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(OrderStatus::Dikirim, $order->fresh()->status);
    }

    /**
     * Test 17: Pencegahan LazyLoadingViolationException saat Model::preventLazyLoading(true) aktif.
     */
    public function test_no_lazy_loading_violation_when_strict_mode_is_enabled(): void
    {
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(true);

        $order = $this->createSampleOrder(OrderStatus::Selesai, $this->customer);
        $order->update([
            'confirmed_at'          => now()->subDays(3),
            'shipping_date'         => now()->subDays(2),
            'tracking_number'       => 'JT9988776655',
            'delivered_at'          => now()->subDay(),
            'delivery_confirmed_by' => $this->customer->id,
        ]);

        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => OrderStatus::MenungguKonfirmasi->value,
            'created_at'  => now()->subDays(4),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::MenungguKonfirmasi->value,
            'to_status'   => OrderStatus::Diproses->value,
            'created_at'  => now()->subDays(3),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::Diproses->value,
            'to_status'   => OrderStatus::Dikirim->value,
            'created_at'  => now()->subDays(2),
        ]);
        $order->statusLogs()->create([
            'from_status' => OrderStatus::Dikirim->value,
            'to_status'   => OrderStatus::Selesai->value,
            'note'        => 'Pembeli mengonfirmasi penerimaan barang',
            'created_at'  => now()->subDay(),
        ]);

        // Customer show
        $response = $this->actingAs($this->customer)->get(route('customer.orders.show', $order->order_number));
        $response->assertOk();
        $response->assertSee('Alur & Progres Pesanan', false);

        // Admin show
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.orders.show', $order));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('Alur & Progres Pesanan', false);

        // Public track
        $responsePublic = $this->post(route('order.track.submit'), [
            'order_number'   => $order->order_number,
            'customer_phone' => $order->customer_phone,
        ]);
        $responsePublic->assertOk();
        $responsePublic->assertSee('Alur & Progres Pesanan', false);
    }
}

