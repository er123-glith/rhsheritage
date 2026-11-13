<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Services\ReviewModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewModerationService $moderationService
    ) {}

    /**
     * Tampilkan formulir ulasan untuk pesanan selesai.
     */
    public function create(string $orderNumber): View|RedirectResponse
    {
        $order = Order::with(['items.variant.product', 'review'])
            ->where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== OrderStatus::Selesai) {
            return redirect()->route('customer.orders.show', $orderNumber)
                ->with('error', 'Hanya pesanan yang telah berstatus Selesai yang dapat diberikan ulasan toko.');
        }

        if ($order->review) {
            return redirect()->route('customer.orders.show', $orderNumber)
                ->with('info', 'Anda telah memberikan ulasan untuk pesanan ini.');
        }

        return view('customer.reviews.create', compact('order'));
    }

    /**
     * Simpan ulasan customer dengan pemeriksaan moderasi berlapis.
     */
    public function store(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::with('review')
            ->where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== OrderStatus::Selesai) {
            return redirect()->route('customer.orders.show', $orderNumber)
                ->with('error', 'Hanya pesanan berstatus Selesai yang dapat diulas.');
        }

        if ($order->review) {
            return redirect()->route('customer.orders.show', $orderNumber)
                ->with('error', 'Satu pesanan hanya dapat diberikan satu kali ulasan toko.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rating.required' => 'Silakan pilih rating kepuasan (1 sampai 5 bintang).',
            'rating.between'  => 'Rating harus bernilai antara 1 sampai 5 bintang.',
            'review.required' => 'Isi ulasan wajib dituliskan.',
            'review.min'      => 'Ulasan harus memiliki minimal 10 karakter bermakna.',
            'review.max'      => 'Ulasan tidak boleh melebihi 1.000 karakter.',
        ]);

        // Jalankan moderasi berlapis
        $moderation = $this->moderationService->moderate($validated['review'], (int) $validated['rating']);

        if ($moderation['action'] === 'reject') {
            return back()
                ->withInput()
                ->with('error', 'Ulasan tidak dapat dikirim: ' . $moderation['reason']);
        }

        $isPending = ($moderation['action'] === 'pending');

        $review = Review::create([
            'user_id'           => auth()->id(),
            'order_id'          => $order->id,
            'rating'            => (int) $validated['rating'],
            'review'            => $moderation['clean_text'],
            'moderation_status' => $isPending ? Review::STATUS_PENDING_REVIEW : Review::STATUS_PUBLISHED,
            'moderation_reason' => $moderation['reason'],
            'published_at'      => $isPending ? null : now(),
        ]);

        if ($isPending) {
            return redirect()->route('customer.orders.show', $orderNumber)
                ->with('success', 'Ulasan Anda telah diterima dan masuk ke antrean peninjauan admin sebelum ditampilkan.');
        }

        return redirect()->route('customer.orders.show', $orderNumber)
            ->with('success', 'Terima kasih atas ulasan Anda! Pengalaman berbelanja Anda telah berhasil dipublikasikan.');
    }
}
