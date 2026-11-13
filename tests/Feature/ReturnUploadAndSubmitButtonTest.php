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
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnUploadAndSubmitButtonTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $otherCustomer;
    private User $admin;
    private Order $order;
    private OrderItem $orderItem;

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

        $this->customer = User::factory()->create([
            'name'     => 'Customer RHS',
            'email'    => 'customer@example.com',
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $this->otherCustomer = User::factory()->create([
            'name'     => 'Other Customer',
            'email'    => 'other@example.com',
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

        $variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'RHS-SNG-09',
            'variant_label'  => 'Ukuran 9',
            'attribute_map'  => ['Ukuran' => '9'],
            'price'          => 150000,
            'stock_quantity' => 15,
            'is_active'      => true,
        ]);

        $zone = \App\Models\ShippingZone::create([
            'name'              => 'Pulau Jawa',
            'base_rate'         => 15000,
            'rate_per_extra_kg' => 7000,
            'sort_order'        => 1,
            'is_active'         => true,
        ]);

        $this->order = Order::create([
            'order_number'       => 'ORD-RET-TEST-001',
            'user_id'            => $this->customer->id,
            'customer_name'      => $this->customer->name,
            'customer_email'     => $this->customer->email,
            'customer_phone'     => '081234567890',
            'address_line'       => 'Jl. Merdeka No. 45',
            'city'               => 'Jakarta Pusat',
            'province'           => 'DKI Jakarta',
            'postal_code'        => '10110',
            'shipping_zone_id'   => $zone->id,
            'shipping_zone_name' => $zone->name,
            'status'             => OrderStatus::Dikirim,
            'subtotal'           => 300000,
            'shipping_cost'      => 20000,
            'total'              => 320000,
            'payment_status'     => 'settlement',
            'total_weight_gram'  => 500,
        ]);

        $this->orderItem = OrderItem::create([
            'order_id'           => $this->order->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'variant_label'      => 'Size 9 / Hitam',
            'unit_price'         => 150000,
            'quantity'           => 2,
            'subtotal'           => 300000,
        ]);
    }

    /**
     * 1. Upload video valid 10 MB.
     */
    public function test_upload_valid_video_10mb_and_valid_photos_succeeds(): void
    {
        $video10Mb = UploadedFile::fake()->create('unboxing_10mb.mp4', 10 * 1024, 'video/mp4');
        $photo1 = UploadedFile::fake()->image('bukti1.jpg', 800, 800);

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Barang cacat jahitan saat unboxing paket pertama kali.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video10Mb,
            'photos'         => [$photo1],
            'agreement'      => '1',
        ]);

        $response->assertStatus(302);
        $return = OrderReturn::where('order_id', $this->order->id)->first();
        $this->assertNotNull($return);
        $this->assertEquals('replacement', $return->solution);

        $videoEvidence = $return->unboxingVideo();
        $this->assertNotNull($videoEvidence);
        Storage::disk('local')->assertExists($videoEvidence->file_path);
    }

    /**
     * 2. Upload video valid mendekati 100 MB (95 MB).
     */
    public function test_upload_valid_video_near_100mb_succeeds(): void
    {
        $video95Mb = UploadedFile::fake()->create('unboxing_95mb.mp4', 95 * 1024, 'video/mp4');
        $photo = UploadedFile::fake()->image('bukti.png', 800, 800);

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'refund',
            'reason'         => 'tidak_sesuai',
            'description'    => 'Barang yang diterima berbeda spesifikasi katalog.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video95Mb,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertStatus(302);
        $return = OrderReturn::where('order_id', $this->order->id)->first();
        $this->assertNotNull($return);
        $this->assertEquals('refund', $return->solution);
    }

    /**
     * 3. Upload video di atas 100 MB (105 MB) ditolak validasi.
     */
    public function test_upload_video_over_100mb_is_rejected(): void
    {
        $video105Mb = UploadedFile::fake()->create('unboxing_oversize.mp4', 105 * 1024, 'video/mp4');
        $photo = UploadedFile::fake()->image('bukti.jpg', 800, 800);

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan masalah pengajuan retur.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video105Mb,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['unboxing_video']);
        $this->assertDatabaseCount('returns', 0);
    }

    /**
     * 4. Upload foto di atas 5 MB ditolak validasi.
     */
    public function test_upload_photo_over_5mb_is_rejected(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $oversizedPhoto = UploadedFile::fake()->create('foto_berat.jpg', 6 * 1024, 'image/jpeg');

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan masalah retur barang.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$oversizedPhoto],
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['photos.0']);
        $this->assertDatabaseCount('returns', 0);
    }

    /**
     * 5. Upload dengan format video tidak sah (.avi / .mkv) ditolak validasi.
     */
    public function test_upload_with_invalid_video_format_is_rejected(): void
    {
        $invalidVideo = UploadedFile::fake()->create('unboxing.avi', 5000, 'video/x-msvideo');
        $photo = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan masalah retur barang.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $invalidVideo,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['unboxing_video']);
        $this->assertDatabaseCount('returns', 0);
    }

    /**
     * 6. Upload dengan format foto tidak sah (.pdf / .txt) ditolak validasi.
     */
    public function test_upload_with_invalid_photo_format_is_rejected(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $invalidPhoto = UploadedFile::fake()->create('bukti.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan masalah retur barang.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$invalidPhoto],
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['photos.0']);
        $this->assertDatabaseCount('returns', 0);
    }

    /**
     * 7. Pengajuan tanpa video unboxing ditolak.
     */
    public function test_submission_without_video_is_rejected(): void
    {
        $photo = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan masalah retur barang.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            // unboxing_video tidak disertakan
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['unboxing_video']);
        $this->assertDatabaseCount('returns', 0);
    }

    /**
     * 8. Pengajuan tanpa foto bukti fisik ditolak.
     */
    public function test_submission_without_photos_is_rejected(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan masalah retur barang.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            // photos tidak disertakan
            'agreement'      => '1',
        ]);

        $response->assertSessionHasErrors(['photos']);
        $this->assertDatabaseCount('returns', 0);
    }

    /**
     * 9. Total request melebihi batas (PostTooLargeException) mengembalikan respon ramah tanpa raw PHP warning.
     */
    public function test_total_request_exceeding_post_limit_returns_413(): void
    {
        // Pengujian via Web form POST
        $responseWeb = $this->actingAs($this->customer)
            ->withServerVariables(['CONTENT_LENGTH' => 999999999])
            ->from(route('customer.returns.create', $this->order->order_number))
            ->post(route('customer.returns.store'), []);

        $responseWeb->assertStatus(413);
        $responseWeb->assertSee('Ukuran file terlalu besar. Silakan kompres atau pilih file yang lebih kecil, kemudian coba kembali.');
        $responseWeb->assertDontSee('PHP Warning');
        $responseWeb->assertDontSee('PostTooLargeException');

        // Pengujian via AJAX / JSON POST
        $responseJson = $this->actingAs($this->customer)
            ->withServerVariables(['CONTENT_LENGTH' => 999999999])
            ->postJson(route('customer.returns.store'), []);

        $responseJson->assertStatus(413);
        $responseJson->assertJson([
            'error' => 'post_too_large',
        ]);
    }

    /**
     * 10. Pengiriman via AJAX menghasilkan respon JSON berstruktur dengan redirect dan status pesan.
     */
    public function test_ajax_submission_returns_json_with_redirect_and_flashed_session(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $photo = UploadedFile::fake()->image('foto.jpg', 600, 600);

        $response = $this->actingAs($this->customer)->postJson(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Pengajuan retur via AJAX dengan indikator progres upload.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'return_number',
            'redirect',
        ]);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * 11. Pengajuan Replacement berhasil dan status awal MenungguReview.
     */
    public function test_replacement_submission_succeeds(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $photo = UploadedFile::fake()->image('foto.jpg');

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'salah_varian',
            'description'    => 'Minta penggantian barang karena ukuran yang dikirim keliru.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertStatus(302);
        $return = OrderReturn::where('order_id', $this->order->id)->first();
        $this->assertNotNull($return);
        $this->assertEquals('replacement', $return->solution);
        $this->assertEquals(ReturnStatus::MenungguReview, $return->status);
        $this->assertNull($return->refund);
    }

    /**
     * 12. Pengajuan Refund berhasil dan membuat entri ReturnRefund.
     */
    public function test_refund_submission_succeeds_and_creates_return_refund(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $photo = UploadedFile::fake()->image('foto.jpg');

        $response = $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'refund',
            'reason'         => 'rusak_cacat',
            'description'    => 'Minta pengembalian dana karena produk rusak total.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 2,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertStatus(302);
        $return = OrderReturn::where('order_id', $this->order->id)->first();
        $this->assertNotNull($return);
        $this->assertEquals('refund', $return->solution);
        $this->assertNotNull($return->refund);
        $this->assertEquals(300000, $return->refund->amount);
        $this->assertEquals(RefundStatus::BelumDiproses, $return->refund->status);
    }

    /**
     * 13. Pesanan customer lain tidak dapat digunakan untuk mengajukan retur (Otorisasi & Proteksi Akses).
     */
    public function test_other_customer_cannot_submit_return_for_order(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $photo = UploadedFile::fake()->image('foto.jpg');

        // Customer lain mencoba submit retur untuk order milik customer pertama
        $response = $this->actingAs($this->otherCustomer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Percobaan pengajuan retur pesanan bukan miliknya.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('returns', 0);
    }

    /**
     * 14. Berkas sensitif (video & foto) tersimpan di storage lokal privat dan tidak dapat diakses publik.
     */
    public function test_sensitive_return_files_are_stored_privately_and_shielded(): void
    {
        $video = UploadedFile::fake()->create('unboxing.mp4', 5000, 'video/mp4');
        $photo = UploadedFile::fake()->image('foto.jpg');

        $this->actingAs($this->customer)->post(route('customer.returns.store'), [
            'order_id'       => $this->order->id,
            'solution'       => 'replacement',
            'reason'         => 'rusak_cacat',
            'description'    => 'Penjelasan masalah retur barang.',
            'items'          => [
                [
                    'id'  => $this->orderItem->id,
                    'qty' => 1,
                ],
            ],
            'unboxing_video' => $video,
            'photos'         => [$photo],
            'agreement'      => '1',
        ]);

        $return = OrderReturn::where('order_id', $this->order->id)->first();
        $evidence = $return->evidences->first();

        // Verifikasi file berada dalam disk lokal (privat, bukan storage/public)
        $this->assertStringStartsWith('secure_proofs/returns/', $evidence->file_path);

        // Reset auth ke guest untuk menguji proteksi akses tamu
        auth()->logout();

        // Guest mencoba akses secure-proof (harus dialihkan ke login)
        $guestResponse = $this->get(route('secure.proof', [
            'type'     => 'return_evidence',
            'id'       => $evidence->id,
            'filename' => basename($evidence->file_path),
        ]));
        $guestResponse->assertRedirect('/login');

        // Customer lain mencoba akses secure-proof
        $otherResponse = $this->actingAs($this->otherCustomer)->get(route('secure.proof', [
            'type'     => 'return_evidence',
            'id'       => $evidence->id,
            'filename' => basename($evidence->file_path),
        ]));
        $otherResponse->assertStatus(403);
    }

    /**
     * 15. Form pengajuan retur menampilkan tombol submit dan sticky action footer responsif.
     */
    public function test_return_form_renders_submit_button_and_sticky_action_footer(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.returns.create', $this->order->order_number));

        $response->assertStatus(200);
        $response->assertSee('Kirim Pengajuan Retur');
        $response->assertSee('STICKY ACTION FOOTER BAR');
        $response->assertSee('x-data="returnFormHandler()"', false);
        $response->assertSee('function returnFormHandler()', false);
        $response->assertSee('form="returnForm"', false);
        $response->assertSee('type="submit"', false);
        $response->assertSee('md:left-72', false);
        $response->assertSee('fixed bottom-0', false);
        $response->assertSee('multipart/form-data', false);
    }

    /**
     * 16. Admin dapat membuka halaman daftar retur (/admin/retur) tanpa error badgeClass.
     */
    public function test_admin_can_view_returns_index_with_badge_class(): void
    {
        $return = OrderReturn::create([
            'return_number' => 'RET-ADM-001',
            'order_id'      => $this->order->id,
            'user_id'       => $this->customer->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Deskripsi masalah retur.',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $this->orderItem->id,
            'product_variant_id' => $this->orderItem->product_variant_id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.returns.index'));

        $response->assertStatus(200);
        $response->assertSee('RET-ADM-001');
        $response->assertSee($return->status->label());
        $response->assertSee($return->status->badgeClass());
    }

    /**
     * 17. Admin dapat membuka detail retur (/admin/retur/{returnNumber}) dengan refund dan badgeClass.
     */
    public function test_admin_can_view_return_show_with_refund_and_order_badge_class(): void
    {
        $return = OrderReturn::create([
            'return_number' => 'RET-ADM-SHOW',
            'order_id'      => $this->order->id,
            'user_id'       => $this->customer->id,
            'solution'      => 'refund',
            'reason'        => 'rusak_cacat',
            'description'   => 'Deskripsi refund retur.',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $this->orderItem->id,
            'product_variant_id' => $this->orderItem->product_variant_id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        ReturnRefund::create([
            'return_id'      => $return->id,
            'amount'         => 150000,
            'status'         => RefundStatus::BelumDiproses,
            'bank_name'      => 'BCA',
            'account_number' => '1234567890',
            'account_name'   => 'Customer RHS',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.returns.show', $return));

        $response->assertStatus(200);
        $response->assertSee('RET-ADM-SHOW');
        $response->assertSee($return->status->label());
        $response->assertSee($return->refund->status->label());
        $response->assertSee($this->order->order_number);
    }

    /**
     * 18. Admin dapat membuka detail retur (/admin/retur/{returnNumber}) dengan solusi Replacement tanpa error adminReplacementShipment.
     */
    public function test_admin_can_view_return_show_with_replacement_solution(): void
    {
        $return = OrderReturn::create([
            'return_number' => 'RET-ADM-REPL',
            'order_id'      => $this->order->id,
            'user_id'       => $this->customer->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Deskripsi penggantian barang.',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $this->orderItem->id,
            'product_variant_id' => $this->orderItem->product_variant_id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        // Uji view saat barang pengganti belum dikirim
        $response = $this->actingAs($this->admin)->get(route('admin.returns.show', $return));
        $response->assertStatus(200);
        $response->assertSee('RET-ADM-REPL');
        $response->assertSee('2. Kirim Barang Pengganti (Admin)');
        $response->assertSee('Barang pengganti belum dikirim');

        // Uji view saat barang pengganti sudah dikirim oleh admin
        \App\Models\ReturnShipment::create([
            'return_id'           => $return->id,
            'type'                => 'replacement_from_admin',
            'courier'             => 'J&T Express',
            'tracking_number'     => 'JT9988776655',
            'shipped_at'          => now(),
            'shipping_proof_path' => 'secure_proofs/returns/replacements/proof.jpg',
            'notes'               => 'Dikirim via J&T',
        ]);

        $responseWithShipment = $this->actingAs($this->admin)->get(route('admin.returns.show', $return));
        $responseWithShipment->assertStatus(200);
        $responseWithShipment->assertSee('JT9988776655');
        $responseWithShipment->assertSee('Barang Dikirim');
    }

    /**
     * 19. Customer dapat melihat detail pengajuan retur miliknya.
     */
    public function test_customer_can_view_return_show_for_replacement_and_refund(): void
    {
        $return = OrderReturn::create([
            'return_number' => 'RET-CUST-SHOW',
            'order_id'      => $this->order->id,
            'user_id'       => $this->customer->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Deskripsi retur.',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        ReturnItem::create([
            'return_id'          => $return->id,
            'order_item_id'      => $this->orderItem->id,
            'product_variant_id' => $this->orderItem->product_variant_id,
            'quantity'           => 1,
            'unit_price'         => 150000,
            'subtotal'           => 150000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('customer.returns.show', $return->return_number));
        $response->assertStatus(200);
        $response->assertSee('RET-CUST-SHOW');
        $response->assertSee($return->status->label());
    }

    /**
     * 20. Admin returns index menampilkan badge counts dan tab filter berfungsi.
     */
    public function test_admin_returns_index_tabs_and_counts_render(): void
    {
        OrderReturn::create([
            'return_number' => 'RET-CNT-001',
            'order_id'      => $this->order->id,
            'user_id'       => $this->customer->id,
            'solution'      => 'replacement',
            'reason'        => 'rusak_cacat',
            'description'   => 'Retur satu',
            'status'        => ReturnStatus::MenungguReview,
            'agreed_terms'  => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.returns.index'));
        $response->assertStatus(200);
        $response->assertViewHas('counts');
        $response->assertSee('1 Pengajuan Perlu Ditinjau');
    }
}
