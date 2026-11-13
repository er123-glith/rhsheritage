<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\RefundStatus;
use App\Enums\ReturnStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnEvidence;
use App\Models\ReturnItem;
use App\Models\ReturnRefund;
use App\Models\ReturnShipment;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderTrackingAndReturnManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customerA;
    private User $customerB;
    private ProductVariant $variant;
    private ShippingZone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'name'     => 'Admin RHS',
            'email'    => 'admin@rhsheritage.example',
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $this->customerA = User::factory()->create([
            'name'     => 'Pelanggan A',
            'email'    => 'pelanggan.a@example.com',
            'phone'    => '081234567890',
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $this->customerB = User::factory()->create([
            'name'     => 'Pelanggan B',
            'email'    => 'pelanggan.b@example.com',
            'phone'    => '089876543210',
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $category = Category::create([
            'name'       => 'Peci Songkok',
            'slug'       => 'peci-songkok',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Songkok Presiden RHS',
            'slug'        => 'songkok-presiden-rhs',
            'base_price'  => 150000,
            'weight_gram' => 250,
            'is_active'   => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'RHS-SNG-09',
            'variant_label'  => 'Ukuran 9',
            'attribute_map'  => ['Ukuran' => '9'],
            'price'          => 150000,
            'stock_quantity' => 15,
            'is_active'      => true,
        ]);

        $this->zone = ShippingZone::create([
            'name'              => 'Pulau Jawa',
            'base_rate'         => 15000,
            'rate_per_extra_kg' => 7000,
            'sort_order'        => 1,
            'is_active'         => true,
        ]);
    }

    private function createOrderForCustomer(User $customer, OrderStatus $status = OrderStatus::Diproses, int $qty = 2): Order
    {
        $subtotal = 150000 * $qty;
        $shipping = 15000;

        $order = Order::create([
            'user_id'              => $customer->id,
            'order_number'         => 'RHS-ORD-' . strtoupper(uniqid()),
            'customer_name'        => $customer->name,
            'customer_phone'       => $customer->phone ?? '081234567890',
            'customer_email'       => $customer->email,
            'address_line'         => 'Jl. Heritage No. 123',
            'city'                 => 'Surabaya',
            'province'             => 'Jawa Timur',
            'postal_code'          => '60111',
            'shipping_zone_id'     => $this->zone->id,
            'shipping_zone_name'   => $this->zone->name,
            'total_weight_gram'    => 250 * $qty,
            'subtotal'             => $subtotal,
            'shipping_cost'        => $shipping,
            'total'                => $subtotal + $shipping,
            'status'               => $status,
        ]);

        OrderItem::create([
            'order_id'           => $order->id,
            'product_variant_id' => $this->variant->id,
            'product_name'       => $this->variant->product->name,
            'variant_label'      => $this->variant->variant_label,
            'unit_price'         => 150000,
            'quantity'           => $qty,
            'subtotal'           => $subtotal,
        ]);

        return $order;
    }

    /**
     * 1. Layout Sidebar Fixed Desktop Testing
     */
    public function test_admin_and_customer_layouts_render_fixed_sidebar_structure(): void
    {
        $adminResp = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminResp->assertStatus(200);
        $adminResp->assertSee('w-72');
        $adminResp->assertSee('md:pl-72');

        $customerResp = $this->actingAs($this->customerA)->get(route('customer.dashboard'));
        $customerResp->assertStatus(200);
        $customerResp->assertSee('w-72');
        $customerResp->assertSee('md:pl-72');
    }

    /**
     * 2. Order Tracking & Manual Refresh
     */
    public function test_customer_can_view_order_tracking_and_manual_refresh(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Diproses);

        $response = $this->actingAs($this->customerA)->get(route('customer.orders.show', $order->order_number));
        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee('Muat Ulang Status');
    }

    /**
     * 3. Shipping by Admin (J&T Express)
     */
    public function test_admin_can_ship_order_with_jt_express_details_and_proof(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Diproses);

        $proofFile = UploadedFile::fake()->image('resi_jt.jpg', 600, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.ship', $order), [
            'tracking_number' => 'JT9988776655',
            'shipping_date'   => '2026-11-14',
            'shipping_proof'  => $proofFile,
            'shipping_note'   => 'Paket dikirim dengan bubble wrap dan stiker fragile.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $order->refresh();
        $this->assertEquals(OrderStatus::Dikirim, $order->status);
        $this->assertEquals('J&T Express', $order->courier_name);
        $this->assertEquals('JT9988776655', $order->tracking_number);
        $this->assertEquals('2026-11-14', $order->shipping_date?->format('Y-m-d'));
        $this->assertNotNull($order->shipping_proof_path);
        Storage::disk('local')->assertExists($order->shipping_proof_path);
    }

    public function test_admin_ship_order_validation_rules(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Diproses);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.ship', $order), [
            'tracking_number' => '',
            'shipping_date'   => '',
        ]);

        $response->assertSessionHasErrors(['tracking_number', 'shipping_date', 'shipping_proof']);
    }

    public function test_non_admin_cannot_access_admin_shipping_route(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Diproses);

        $response = $this->actingAs($this->customerA)->post(route('admin.orders.ship', $order), [
            'tracking_number' => 'JT1111111111',
            'shipping_date'   => '2026-11-14',
            'shipping_proof'  => UploadedFile::fake()->image('resi.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_directly_complete_normal_order(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.status', $order), [
            'status' => 'selesai',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals(OrderStatus::Dikirim, $order->status);
    }

    /**
     * 4. Customer Delivery Confirmation
     */
    public function test_customer_can_confirm_delivery_with_proof_and_agreement(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim);
        $order->update([
            'courier_name'    => 'J&T Express',
            'tracking_number' => 'JT1234567890',
        ]);

        $photoProof = UploadedFile::fake()->image('barang_diterima.jpg', 600, 600);

        $response = $this->actingAs($this->customerA)->post(route('customer.orders.confirm-delivery', $order->order_number), [
            'delivery_proof' => $photoProof,
            'agreement'      => '1',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $order->refresh();
        $this->assertEquals(OrderStatus::Selesai, $order->status);
        $this->assertNotNull($order->delivered_at);
        $this->assertEquals($this->customerA->id, $order->delivery_confirmed_by);
        $this->assertNotNull($order->delivery_proof_path);
        Storage::disk('local')->assertExists($order->delivery_proof_path);
    }

    public function test_customer_delivery_confirmation_validation(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim);

        $response = $this->actingAs($this->customerA)->post(route('customer.orders.confirm-delivery', $order->order_number), [
            'agreement' => '0',
        ]);

        $response->assertSessionHasErrors(['delivery_proof', 'agreement']);
    }

    public function test_other_customer_cannot_confirm_delivery_of_different_customer_order(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim);

        $response = $this->actingAs($this->customerB)->post(route('customer.orders.confirm-delivery', $order->order_number), [
            'delivery_proof' => UploadedFile::fake()->image('proof.jpg'),
            'agreement'      => '1',
        ]);

        $response->assertStatus(403);
    }

    /**
     * 5. Return Submission & Mandatory Proofs
     */
    public function test_customer_can_submit_return_request_with_unboxing_video_and_photos(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $photo = UploadedFile::fake()->image('rusak.jpg', 600, 600);

        $response = $this->actingAs($this->customerA)->post(route('customer.returns.store'), [
            'order_id'       => $order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Saat kemasan dibuka, terlihat jahitan bagian samping terlepas.',
            'items'          => [
                [
                    'id'  => $orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return = OrderReturn::where('order_id', $order->id)->first();
        $this->assertNotNull($return);
        $this->assertEquals(ReturnStatus::MenungguReview, $return->status);
        $this->assertEquals('replacement', $return->solution);
        $this->assertEquals(1, $return->items()->count());

        // Verifikasi video dan foto tersimpan di storage lokal privat
        $this->assertNotNull($return->unboxingVideo());
        Storage::disk('local')->assertExists($return->unboxingVideo()->file_path);
        $this->assertCount(1, $return->photos());
    }

    public function test_return_request_strictly_requires_unboxing_video(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        $photo = UploadedFile::fake()->image('rusak.jpg', 600, 600);

        $response = $this->actingAs($this->customerA)->post(route('customer.returns.store'), [
            'order_id'       => $order->id,
            'solution'       => 'refund',
            'reason'         => 'salah_varian',
            'description'    => 'Ukuran tidak sesuai dengan pesanan.',
            'items'          => [
                [
                    'id'  => $orderItem->id,
                    'qty' => 1,
                ],
            ],
            // Tanpa unboxing video!
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['unboxing_video']);
        $this->assertDatabaseCount('returns', 0);
    }

    public function test_return_request_strictly_requires_photo_evidence(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');

        $response = $this->actingAs($this->customerA)->post(route('customer.returns.store'), [
            'order_id'       => $order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan cacat produk secara mendetail.',
            'items'          => [
                [
                    'id'  => $orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            // Tanpa foto!
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['photos']);
        $this->assertDatabaseCount('returns', 0);
    }

    public function test_return_request_rejects_exceeding_purchased_quantity(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $photo = UploadedFile::fake()->image('rusak.jpg', 600, 600);

        $response = $this->actingAs($this->customerA)->post(route('customer.returns.store'), [
            'order_id'       => $order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan cacat barang yang diterima.',
            'items'          => [
                [
                    'id'  => $orderItem->id,
                    'qty' => 5, // Melebihi kuantitas beli 2!
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('returns', 0);
    }

    public function test_customer_cannot_confirm_delivery_when_active_return_exists(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);

        // Buat active return
        $return = OrderReturn::create([
            'return_number' => 'RET-ACTIVE-001',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Detail kerusakan',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        $response = $this->actingAs($this->customerA)->post(route('customer.orders.confirm-delivery', $order->order_number), [
            'delivery_proof' => UploadedFile::fake()->image('proof.jpg'),
            'agreement'      => '1',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertNotEquals(OrderStatus::Selesai, $order->status);
    }

    /**
     * 6. Admin Return Review
     */
    public function test_admin_can_review_return_approve(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-REV-APP',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'Barang cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.review', $return), [
            'action'      => 'approve',
            'admin_notes' => 'Disetujui. Kirim ke gudang.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::MenungguBarangBalik, $return->status);
    }

    public function test_admin_can_review_return_reject_with_reason(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-REV-REJ',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'Barang cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.review', $return), [
            'action'           => 'reject',
            'rejection_reason' => 'Kerusakan akibat kelalaian pemakaian setelah unboxing selesai.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::Ditolak, $return->status);
        $this->assertEquals('Kerusakan akibat kelalaian pemakaian setelah unboxing selesai.', $return->rejection_reason);
    }

    public function test_admin_reject_return_requires_reason(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-REV-NOREAS',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'Barang cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.review', $return), [
            'action'           => 'reject',
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors(['rejection_reason']);
    }

    public function test_admin_can_request_additional_info(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-REV-INFO',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'Barang cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.review', $return), [
            'action'      => 'need_info',
            'admin_notes' => 'Mohon berikan foto label barcode kemasan dengan jelas.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::PerluInformasi, $return->status);
        $this->assertEquals('Mohon berikan foto label barcode kemasan dengan jelas.', $return->admin_notes);
    }

    /**
     * 7. Return Shipment & Warehouse Verification
     */
    public function test_customer_can_upload_return_shipment_details_via_jt(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-SHIP-CUST',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::MenungguBarangBalik,
            'agreed_terms'  => true,
        ]);

        $proof = UploadedFile::fake()->image('resi_balik.jpg', 600, 600);

        $response = $this->actingAs($this->customerA)->post(route('customer.returns.ship', $return), [
            'tracking_number' => 'JT5544332211',
            'shipping_date'   => '2026-11-14',
            'shipping_proof'  => $proof,
            'notes'           => 'Barang sudah dikirim di drop point J&T terdekat.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::BarangDiperiksa, $return->status);
        $this->assertEquals('JT5544332211', $return->customerShipment()->tracking_number);
    }

    public function test_admin_can_verify_returned_goods_at_warehouse(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-VERIF-WH',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::MenungguBarangBalik,
            'agreed_terms'  => true,
        ]);

        ReturnShipment::create([
            'return_id'           => $return->id,
            'type'                => 'return_from_customer',
            'courier'             => 'J&T Express',
            'tracking_number'     => 'JT5544332211',
            'shipped_at'          => now(),
            'shipping_proof_path' => 'secure_proofs/sample.jpg',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.verify-received', $return), [
            'notes' => 'Kemasan tiba utuh, isi sesuai komplain.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::BarangDiperiksa, $return->status);
    }

    /**
     * 8. Replacement Fulfillment & Stock Decrement
     */
    public function test_admin_processes_replacement_and_decrements_variant_stock(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        $initialStock = $this->variant->stock_quantity; // 15

        $return = OrderReturn::create([
            'return_number' => 'RET-REP-PROC',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'Barang cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::BarangDiperiksa,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $orderItem->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        $proof = UploadedFile::fake()->image('resi_pengganti.jpg', 600, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.process-replacement', $return), [
            'tracking_number' => 'JT7788990011',
            'shipping_date'   => '2026-11-14',
            'shipping_proof'  => $proof,
            'notes'           => 'Barang baru telah dikirim dengan nomor resi terlampir.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::DalamPenyelesaian, $return->status);

        // Stok varian harus berkurang 1
        $this->variant->refresh();
        $this->assertEquals($initialStock - 1, $this->variant->stock_quantity);
    }

    public function test_replacement_rejected_if_variant_stock_insufficient(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        // Habiskan stok
        $this->variant->update(['stock_quantity' => 0]);

        $return = OrderReturn::create([
            'return_number' => 'RET-NOSTOCK',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'Barang cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::BarangDiperiksa,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $orderItem->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        $proof = UploadedFile::fake()->image('resi_pengganti.jpg', 600, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.process-replacement', $return), [
            'tracking_number' => 'JT7788990011',
            'shipping_date'   => '2026-11-14',
            'shipping_proof'  => $proof,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $return->refresh();
        $this->assertNotEquals(ReturnStatus::DalamPenyelesaian, $return->status);
    }

    public function test_customer_can_confirm_replacement_received(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-REP-CONF',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::DalamPenyelesaian,
            'agreed_terms'  => true,
        ]);

        $response = $this->actingAs($this->customerA)->post(route('customer.returns.confirm-replacement', $return), [
            'agreement' => '1',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::Selesai, $return->status);
    }

    /**
     * 9. Refund Fulfillment & Max Refund Validation
     */
    public function test_admin_processes_refund_with_valid_amount_and_transfer_proof(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        $return = OrderReturn::create([
            'return_number' => 'RET-REF-PROC',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'refund',
            'reason'        => 'Barang habis varian',
            'description'   => 'Detail',
            'status'        => ReturnStatus::BarangDiperiksa,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $orderItem->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        $proof = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.process-refund', $return), [
            'bank_name'         => 'BCA',
            'account_number'    => '8820192837',
            'account_name'      => 'Pelanggan A',
            'amount'            => 150000,
            'payment_reference' => 'TRX-REF-9921',
            'proof'             => $proof,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::DalamPenyelesaian, $return->status);
        $this->assertNotNull($return->refund);
        $this->assertEquals(RefundStatus::MenungguKonfirmasi, $return->refund->status);
        $this->assertEquals(150000, (float) $return->refund->amount);
    }

    public function test_refund_amount_cannot_exceed_purchased_item_subtotal(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $orderItem = $order->orderItems->first();

        $return = OrderReturn::create([
            'return_number' => 'RET-REF-OVER',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'refund',
            'reason'        => 'Barang habis varian',
            'description'   => 'Detail',
            'status'        => ReturnStatus::BarangDiperiksa,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $orderItem->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        $proof = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.returns.process-refund', $return), [
            'bank_name'         => 'BCA',
            'account_number'    => '8820192837',
            'account_name'      => 'Pelanggan A',
            'amount'            => 250000, // Melebihi nilai item retur Rp150.000!
            'payment_reference' => 'TRX-REF-9921',
            'proof'             => $proof,
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_customer_can_confirm_refund_received(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim, 2);
        $return = OrderReturn::create([
            'return_number' => 'RET-REF-CONF',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'refund',
            'reason'        => 'rusak_cacat',
            'description'   => 'Detail',
            'status'        => ReturnStatus::DalamPenyelesaian,
            'agreed_terms'  => true,
        ]);

        ReturnRefund::create([
            'return_id'         => $return->id,
            'amount'            => 150000,
            'bank_name'         => 'BCA',
            'account_number'    => '8820192837',
            'account_name'      => 'Pelanggan A',
            'status'            => RefundStatus::MenungguKonfirmasi,
            'payment_reference' => 'TRX-REF-9921',
            'transferred_at'    => now(),
        ]);

        $response = $this->actingAs($this->customerA)->post(route('customer.returns.confirm-refund', $return), [
            'agreement' => '1',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $return->refresh();
        $this->assertEquals(ReturnStatus::Selesai, $return->status);
    }

    /**
     * 10. Private Storage Security & Authorization
     */
    public function test_secure_proof_route_denies_guest(): void
    {
        $response = $this->get('/secure-proof/shipping_proof/1/test.jpg');
        $this->assertTrue($response->isRedirect() || $response->status() === 401 || $response->status() === 403);
    }

    public function test_unauthorized_customer_cannot_view_other_customers_secure_proof(): void
    {
        $filePath = 'secure_proofs/returns/evidences/secret_proof.jpg';
        Storage::disk('local')->put($filePath, 'fake image content');

        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim);
        $return = OrderReturn::create([
            'return_number' => 'RET-SEC-001',
            'order_id'      => $order->id,
            'user_id'       => $this->customerA->id,
            'solution'      => 'replacement',
            'reason'        => 'Test',
            'description'   => 'Test',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        $evidence = ReturnEvidence::create([
            'return_id'  => $return->id,
            'file_type'  => 'photo',
            'file_path'  => $filePath,
            'file_name'  => 'secret_proof.jpg',
            'mime_type'  => 'image/jpeg',
            'file_size'  => 1234,
        ]);

        $proofUrl = route('secure.proof', ['type' => 'return_evidence', 'id' => $evidence->id, 'filename' => 'secret_proof.jpg']);

        // Customer B mencoba mengakses bukti milik Customer A
        $response = $this->actingAs($this->customerB)->get($proofUrl);
        $response->assertStatus(403);

        // Customer A (pemilik) boleh mengakses
        $responseA = $this->actingAs($this->customerA)->get($proofUrl);
        $responseA->assertStatus(200);

        // Admin boleh mengakses
        $responseAdmin = $this->actingAs($this->admin)->get($proofUrl);
        $responseAdmin->assertStatus(200);
    }

    /**
     * 11. Public Tracking Shield
     */
    public function test_public_tracking_masks_sensitive_data_and_displays_status(): void
    {
        $order = $this->createOrderForCustomer($this->customerA, OrderStatus::Dikirim);
        $order->update([
            'courier_name'    => 'J&T Express',
            'tracking_number' => 'JT8877665544',
        ]);

        $response = $this->post(route('order.track.submit'), [
            'order_number'   => $order->order_number,
            'customer_phone' => $order->customer_phone,
        ]);

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee('JT8877665544');
        $response->assertSee('J&T Express');
        // Pastikan tidak membocorkan bukti foto privat atau alamat lengkap sensitif
        $response->assertDontSee($order->address_line);
    }
}
