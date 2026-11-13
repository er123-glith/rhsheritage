<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use App\Services\BestSellerService;
use App\Services\ReviewModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageRefactoringTest extends TestCase
{
    use RefreshDatabase;

    protected function createCustomer(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role'     => 'customer',
            'is_owner' => false,
        ], $attributes));
    }

    protected function createAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role'     => 'admin',
            'is_owner' => true,
        ], $attributes));
    }

    protected function createCompletedOrder(User $customer): Order
    {
        $category = Category::create([
            'name'       => 'Songkok Nusantara',
            'slug'       => 'songkok',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Songkok Beludru Premium',
            'slug'        => 'songkok-beludru-premium',
            'base_price'  => 85000,
            'weight_gram' => 300,
            'is_active'   => true,
        ]);

        $variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'SKK-001',
            'variant_label'  => 'Ukuran 9',
            'attribute_map'  => ['ukuran' => '9'],
            'price'          => 85000,
            'stock_quantity' => 20,
            'is_active'      => true,
        ]);

        $order = Order::create([
            'user_id'            => $customer->id,
            'order_number'       => 'RHS-' . date('Ymd') . '-' . rand(1000, 9999),
            'customer_name'      => $customer->name,
            'customer_phone'     => '081234567890',
            'address_line'       => 'Jl. Pahlawan No. 10',
            'city'               => 'Surabaya',
            'province'           => 'Jawa Timur',
            'postal_code'        => '60111',
            'shipping_zone_name' => 'Jawa Timur',
            'subtotal'           => 85000,
            'shipping_cost'      => 15000,
            'total'              => 100000,
            'status'             => OrderStatus::Selesai,
            'total_weight_gram'  => 300,
        ]);

        OrderItem::create([
            'order_id'           => $order->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'variant_label'      => $variant->variant_label,
            'unit_price'         => 85000,
            'quantity'           => 1,
            'subtotal'           => 85000,
        ]);

        return $order;
    }

    public function test_landing_page_renders_with_official_favicon_and_font_awesome(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('favicon.svg');
        $response->assertSee('favicon-32x32.png');
        $response->assertSee('apple-touch-icon.png');
        $response->assertSee('fa-solid fa-crown', false);
        $response->assertSee('fa-solid fa-cart-shopping', false);
    }

    public function test_landing_page_renders_floating_whatsapp_button_with_configured_number(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $configuredNumber = config('whatsapp.destination_number', '6285728979999');
        $response->assertSee('https://wa.me/' . $configuredNumber, false);
        $response->assertSee('fa-brands fa-whatsapp', false);
        $response->assertSee('Hubungi Kami via WhatsApp');
    }

    public function test_guest_cannot_submit_review(): void
    {
        $response = $this->get('/customer/pesanan/RHS-123/ulasan');
        $response->assertRedirect(route('login'));

        $postResponse = $this->post('/customer/pesanan/RHS-123/ulasan', [
            'rating' => 5,
            'review' => 'Kualitas kriya sangat bagus dan jahitannya halus.',
        ]);
        $postResponse->assertRedirect(route('login'));
    }

    public function test_customer_without_completed_order_cannot_submit_review(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);
        $order->update(['status' => OrderStatus::Diproses]);

        $response = $this->actingAs($customer)->get(route('customer.reviews.create', $order->order_number));
        $response->assertRedirect(route('customer.orders.show', $order->order_number));
        $response->assertSessionHas('error');

        $postResponse = $this->actingAs($customer)->post(route('customer.reviews.store', $order->order_number), [
            'rating' => 5,
            'review' => 'Kualitas songkok sangat memuaskan dan kokoh.',
        ]);
        $postResponse->assertRedirect(route('customer.orders.show', $order->order_number));
        $postResponse->assertSessionHas('error');
    }

    public function test_customer_with_completed_order_can_view_review_form(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        $response = $this->actingAs($customer)->get(route('customer.reviews.create', $order->order_number));

        $response->assertStatus(200);
        $response->assertSee('Tulis Ulasan Toko');
        $response->assertSee($order->order_number);
        $response->assertSee($customer->name);
    }

    public function test_customer_cannot_review_other_customers_order(): void
    {
        $customerA = $this->createCustomer(['email' => 'a@example.com']);
        $customerB = $this->createCustomer(['email' => 'b@example.com']);
        $orderA = $this->createCompletedOrder($customerA);

        $response = $this->actingAs($customerB)->get(route('customer.reviews.create', $orderA->order_number));
        $response->assertStatus(404);

        $postResponse = $this->actingAs($customerB)->post(route('customer.reviews.store', $orderA->order_number), [
            'rating' => 5,
            'review' => 'Songkok sangat nyaman dipakai santri.',
        ]);
        $postResponse->assertStatus(404);
    }

    public function test_customer_cannot_submit_duplicate_review_for_same_order(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        Review::create([
            'user_id'           => $customer->id,
            'order_id'          => $order->id,
            'rating'            => 5,
            'review'            => 'Ulasan pertama yang sangat bagus dan rapi.',
            'moderation_status' => Review::STATUS_PUBLISHED,
            'published_at'      => now(),
        ]);

        $response = $this->actingAs($customer)->post(route('customer.reviews.store', $order->order_number), [
            'rating' => 4,
            'review' => 'Ulasan kedua yang seharusnya ditolak sistem.',
        ]);

        $response->assertRedirect(route('customer.orders.show', $order->order_number));
        $response->assertSessionHas('error', 'Satu pesanan hanya dapat diberikan satu kali ulasan toko.');
        $this->assertEquals(1, Review::where('order_id', $order->id)->count());
    }

    public function test_invalid_rating_and_too_short_review_are_rejected(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        // Rating 0
        $response = $this->actingAs($customer)->from(route('customer.reviews.create', $order->order_number))
            ->post(route('customer.reviews.store', $order->order_number), [
                'rating' => 0,
                'review' => 'Barang bagus sekali jahitannya rapi.',
            ]);
        $response->assertSessionHasErrors('rating');

        // Review kurang dari 10 karakter
        $responseShort = $this->actingAs($customer)->from(route('customer.reviews.create', $order->order_number))
            ->post(route('customer.reviews.store', $order->order_number), [
                'rating' => 5,
                'review' => 'Bagus',
            ]);
        $responseShort->assertSessionHasErrors('review');
    }

    public function test_severe_hate_speech_and_phishing_content_are_rejected(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        $moderationService = app(ReviewModerationService::class);

        // Ujaran kebencian kasar
        $hateCheck = $moderationService->moderate('Dasar penjual babi bangsat anjing', 1);
        $this->assertEquals('reject', $hateCheck['action']);

        // Tautan phishing / spam judi
        $spamCheck = $moderationService->moderate('Dapatkan bonus di https://slot-gacor.example.xyz yuk', 5);
        $this->assertEquals('reject', $spamCheck['action']);

        // Submission via controller
        $response = $this->actingAs($customer)->from(route('customer.reviews.create', $order->order_number))
            ->post(route('customer.reviews.store', $order->order_number), [
                'rating' => 1,
                'review' => 'Pelayanan sangat kasar anjing bangsat',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reviews', ['order_id' => $order->id]);
    }

    public function test_legitimate_critical_review_is_accepted_without_bias(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        // Kritik konstruktif rating 2 tanpa kata kasar
        $response = $this->actingAs($customer)->post(route('customer.reviews.store', $order->order_number), [
            'rating' => 2,
            'review' => 'Ukuran songkok agak sempit dari standar, jahitan bagian dalam sedikit kurang rapi tapi bahan beludru cukup bagus.',
        ]);

        $response->assertRedirect(route('customer.orders.show', $order->order_number));
        $this->assertDatabaseHas('reviews', [
            'order_id'          => $order->id,
            'rating'            => 2,
            'moderation_status' => Review::STATUS_PUBLISHED,
        ]);
    }

    public function test_borderline_claim_content_is_queued_for_admin_moderation(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        $response = $this->actingAs($customer)->post(route('customer.reviews.store', $order->order_number), [
            'rating' => 1,
            'review' => 'Barang yang dikirim palsu dan abal-abal, penjual penipu besar saya kecewa sekali.',
        ]);

        $response->assertRedirect(route('customer.orders.show', $order->order_number));
        $this->assertDatabaseHas('reviews', [
            'order_id'          => $order->id,
            'rating'            => 1,
            'moderation_status' => Review::STATUS_PENDING_REVIEW,
        ]);
    }

    public function test_clean_review_is_published_immediately_and_appears_on_homepage(): void
    {
        $customer = $this->createCustomer(['name' => 'Ahmad Fauzi']);
        $order = $this->createCompletedOrder($customer);

        $reviewText = 'Songkok beludru hitam sangat nyaman dan kokoh, bahan adem dipakai seharian.';

        $this->actingAs($customer)->post(route('customer.reviews.store', $order->order_number), [
            'rating' => 5,
            'review' => $reviewText,
        ]);

        $review = Review::where('order_id', $order->id)->first();
        $this->assertNotNull($review);
        $this->assertEquals(Review::STATUS_PUBLISHED, $review->moderation_status);
        $this->assertNotNull($review->published_at);

        // Homepage assert
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee($reviewText);
        $homeResponse->assertSee('Ahmad F.'); // Masked privacy name
        $homeResponse->assertSee('Pembelian Terverifikasi');
    }

    public function test_pending_and_rejected_reviews_do_not_appear_on_homepage(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        $pendingText = 'Ulasan rahasia yang sedang ditinjau moderator.';
        Review::create([
            'user_id'           => $customer->id,
            'order_id'          => $order->id,
            'rating'            => 4,
            'review'            => $pendingText,
            'moderation_status' => Review::STATUS_PENDING_REVIEW,
            'published_at'      => null,
        ]);

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertDontSee($pendingText);
    }

    public function test_homepage_shows_empty_review_state_when_no_reviews_exist(): void
    {
        $this->assertEquals(0, Review::count());

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Belum Ada Ulasan Pelanggan');
        $homeResponse->assertSee('Jadilah pelanggan pertama yang membagikan pengalaman berbelanja di RHS Heritage.');
    }

    public function test_admin_can_view_reviews_and_filter_by_status(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        Review::create([
            'user_id'           => $customer->id,
            'order_id'          => $order->id,
            'rating'            => 5,
            'review'            => 'Barang kriya sangat berkualitas tinggi.',
            'moderation_status' => Review::STATUS_PENDING_REVIEW,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reviews.index', ['status' => 'pending_review']));
        $response->assertStatus(200);
        $response->assertSee('Barang kriya sangat berkualitas tinggi.');
        $response->assertSee('Menunggu Moderasi');
    }

    public function test_admin_can_approve_pending_review(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        $review = Review::create([
            'user_id'           => $customer->id,
            'order_id'          => $order->id,
            'rating'            => 5,
            'review'            => 'Ulasan yang tadinya tertunda moderasi.',
            'moderation_status' => Review::STATUS_PENDING_REVIEW,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reviews.approve', $review->id));

        $response->assertSessionHas('success');
        $review->refresh();
        $this->assertEquals(Review::STATUS_PUBLISHED, $review->moderation_status);
        $this->assertEquals($admin->id, $review->moderated_by);
        $this->assertNotNull($review->published_at);
    }

    public function test_admin_can_reject_review_with_reason(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        $review = Review::create([
            'user_id'           => $customer->id,
            'order_id'          => $order->id,
            'rating'            => 1,
            'review'            => 'Ulasan melanggar aturan etika.',
            'moderation_status' => Review::STATUS_PENDING_REVIEW,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reviews.reject', $review->id), [
            'rejection_reason' => 'Mengandung provokasi yang tidak sesuai fakta transaksi.',
        ]);

        $response->assertSessionHas('success');
        $review->refresh();
        $this->assertEquals(Review::STATUS_REJECTED, $review->moderation_status);
        $this->assertEquals('Mengandung provokasi yang tidak sesuai fakta transaksi.', $review->moderation_reason);
    }

    public function test_admin_can_hide_published_review(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createCompletedOrder($customer);

        $review = Review::create([
            'user_id'           => $customer->id,
            'order_id'          => $order->id,
            'rating'            => 4,
            'review'            => 'Ulasan aktif yang ingin disembunyikan sementara.',
            'moderation_status' => Review::STATUS_PUBLISHED,
            'published_at'      => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reviews.hide', $review->id), [
            'hide_reason' => 'Permintaan peninjauan ulang oleh pemilik.',
        ]);

        $response->assertSessionHas('success');
        $review->refresh();
        $this->assertEquals(Review::STATUS_HIDDEN, $review->moderation_status);
    }

    public function test_best_seller_calculation_uses_actual_transaction_data(): void
    {
        $customer = $this->createCustomer();
        $category = Category::create(['name' => 'Songkok', 'slug' => 'songkok', 'sort_order' => 1]);

        $prodA = Product::create(['category_id' => $category->id, 'name' => 'Produk Populer A', 'slug' => 'produk-a', 'base_price' => 100000, 'weight_gram' => 200, 'is_active' => true]);
        $varA = ProductVariant::create(['product_id' => $prodA->id, 'sku' => 'A-01', 'variant_label' => 'Standard', 'attribute_map' => ['warna' => 'Hitam'], 'price' => 100000, 'stock_quantity' => 50, 'is_active' => true]);

        $prodB = Product::create(['category_id' => $category->id, 'name' => 'Produk Populer B', 'slug' => 'produk-b', 'base_price' => 50000, 'weight_gram' => 200, 'is_active' => true]);
        $varB = ProductVariant::create(['product_id' => $prodB->id, 'sku' => 'B-01', 'variant_label' => 'Standard', 'attribute_map' => ['warna' => 'Putih'], 'price' => 50000, 'stock_quantity' => 50, 'is_active' => true]);

        // Order 1 (Selesai): 5 unit Produk A
        $order1 = Order::create(['user_id' => $customer->id, 'order_number' => 'RHS-ORD-1', 'customer_name' => 'User', 'customer_phone' => '08123', 'address_line' => 'Jl. A', 'city' => 'Sby', 'province' => 'Jatim', 'postal_code' => '601', 'shipping_zone_name' => 'Jatim', 'subtotal' => 500000, 'shipping_cost' => 10000, 'total' => 510000, 'status' => OrderStatus::Selesai, 'total_weight_gram' => 1000]);
        OrderItem::create(['order_id' => $order1->id, 'product_variant_id' => $varA->id, 'product_name' => $prodA->name, 'variant_label' => $varA->variant_label, 'unit_price' => 100000, 'quantity' => 5, 'subtotal' => 500000]);

        // Order 2 (Diproses): 2 unit Produk B
        $order2 = Order::create(['user_id' => $customer->id, 'order_number' => 'RHS-ORD-2', 'customer_name' => 'User', 'customer_phone' => '08123', 'address_line' => 'Jl. B', 'city' => 'Sby', 'province' => 'Jatim', 'postal_code' => '601', 'shipping_zone_name' => 'Jatim', 'subtotal' => 100000, 'shipping_cost' => 10000, 'total' => 110000, 'status' => OrderStatus::Diproses, 'total_weight_gram' => 400]);
        OrderItem::create(['order_id' => $order2->id, 'product_variant_id' => $varB->id, 'product_name' => $prodB->name, 'variant_label' => $varB->variant_label, 'unit_price' => 50000, 'quantity' => 2, 'subtotal' => 100000]);

        $bestSellers = app(BestSellerService::class)->getBestSellers();

        $this->assertCount(2, $bestSellers);
        $this->assertEquals($prodA->id, $bestSellers->first()->id);
        $this->assertEquals(5, $bestSellers->first()->total_sold);
        $this->assertEquals(1, $bestSellers->first()->sales_rank);

        $this->assertEquals($prodB->id, $bestSellers->last()->id);
        $this->assertEquals(2, $bestSellers->last()->total_sold);
        $this->assertEquals(2, $bestSellers->last()->sales_rank);
    }

    public function test_best_seller_excludes_cancelled_orders(): void
    {
        $customer = $this->createCustomer();
        $category = Category::create(['name' => 'Sarung', 'slug' => 'sarung', 'sort_order' => 1]);

        $prod = Product::create(['category_id' => $category->id, 'name' => 'Sarung Batal', 'slug' => 'sarung-batal', 'base_price' => 150000, 'weight_gram' => 500, 'is_active' => true]);
        $var = ProductVariant::create(['product_id' => $prod->id, 'sku' => 'SRG-BTL', 'variant_label' => 'Standard', 'attribute_map' => ['warna' => 'Hijau'], 'price' => 150000, 'stock_quantity' => 10, 'is_active' => true]);

        // Order berstatus Dibatalkan dengan kuantitas besar (100 unit)
        $cancelledOrder = Order::create(['user_id' => $customer->id, 'order_number' => 'RHS-CANCEL-1', 'customer_name' => 'User', 'customer_phone' => '08123', 'address_line' => 'Jl. C', 'city' => 'Sby', 'province' => 'Jatim', 'postal_code' => '601', 'shipping_zone_name' => 'Jatim', 'subtotal' => 15000000, 'shipping_cost' => 10000, 'total' => 15010000, 'status' => OrderStatus::Dibatalkan, 'total_weight_gram' => 50000]);
        OrderItem::create(['order_id' => $cancelledOrder->id, 'product_variant_id' => $var->id, 'product_name' => $prod->name, 'variant_label' => $var->variant_label, 'unit_price' => 150000, 'quantity' => 100, 'subtotal' => 15000000]);

        $bestSellers = app(BestSellerService::class)->getBestSellers();

        // Tidak boleh ada di ranking
        $this->assertTrue($bestSellers->where('id', $prod->id)->isEmpty());
    }
}
