<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Tampilkan daftar seluruh ulasan toko dari pelanggan.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = $request->input('q');

        // Hitung total per status
        $allCount = Review::count();
        $publishedCount = Review::where('moderation_status', Review::STATUS_PUBLISHED)->count();
        $pendingCount = Review::where('moderation_status', Review::STATUS_PENDING_REVIEW)->count();
        $rejectedCount = Review::where('moderation_status', Review::STATUS_REJECTED)->count();
        $hiddenCount = Review::where('moderation_status', Review::STATUS_HIDDEN)->count();

        $query = Review::with(['user', 'order', 'moderator'])->latest();

        if ($status && in_array($status, [
            Review::STATUS_PUBLISHED,
            Review::STATUS_PENDING_REVIEW,
            Review::STATUS_REJECTED,
            Review::STATUS_HIDDEN,
        ], true)) {
            $query->where('moderation_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('review', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('order', fn($o) => $o->where('order_number', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact(
            'reviews',
            'status',
            'allCount',
            'publishedCount',
            'pendingCount',
            'rejectedCount',
            'hiddenCount'
        ));
    }

    /**
     * Menyetujui dan mempublikasikan ulasan pelanggan.
     */
    public function approve(Request $request, Review $review): RedirectResponse
    {
        $oldStatus = $review->moderation_status;

        $review->update([
            'moderation_status' => Review::STATUS_PUBLISHED,
            'moderated_by'      => auth()->id(),
            'moderated_at'      => now(),
            'published_at'      => $review->published_at ?? now(),
            'moderation_reason' => null,
        ]);

        AdminActivityLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $review->user_id,
            'action'         => 'approve_review',
            'description'    => "Menyetujui publikasi ulasan pada pesanan #{$review->order?->order_number} (Rating {$review->rating}★)",
            'changes_json'   => [
                'review_id'  => $review->id,
                'old_status' => $oldStatus,
                'new_status' => Review::STATUS_PUBLISHED,
            ],
            'status'         => 'success',
            'ip_address'     => $request->ip(),
        ]);

        return back()->with('success', "Ulasan pesanan #{$review->order?->order_number} berhasil disetujui dan dipublikasikan.");
    }

    /**
     * Menolak ulasan pelanggan yang melanggar dengan alasan.
     */
    public function reject(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan ulasan wajib dicantumkan.',
        ]);

        $oldStatus = $review->moderation_status;

        $review->update([
            'moderation_status' => Review::STATUS_REJECTED,
            'moderation_reason' => $validated['rejection_reason'],
            'moderated_by'      => auth()->id(),
            'moderated_at'      => now(),
            'published_at'      => null,
        ]);

        AdminActivityLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $review->user_id,
            'action'         => 'reject_review',
            'description'    => "Menolak ulasan pada pesanan #{$review->order?->order_number} dengan alasan: {$validated['rejection_reason']}",
            'changes_json'   => [
                'review_id'  => $review->id,
                'old_status' => $oldStatus,
                'new_status' => Review::STATUS_REJECTED,
                'reason'     => $validated['rejection_reason'],
            ],
            'status'         => 'success',
            'ip_address'     => $request->ip(),
        ]);

        return back()->with('success', "Ulasan pesanan #{$review->order?->order_number} telah ditolak.");
    }

    /**
     * Menyembunyikan ulasan yang sebelumnya terbit.
     */
    public function hide(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'hide_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $oldStatus = $review->moderation_status;
        $reason = $validated['hide_reason'] ?? 'Disembunyikan oleh administrator.';

        $review->update([
            'moderation_status' => Review::STATUS_HIDDEN,
            'moderation_reason' => $reason,
            'moderated_by'      => auth()->id(),
            'moderated_at'      => now(),
        ]);

        AdminActivityLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $review->user_id,
            'action'         => 'hide_review',
            'description'    => "Menyembunyikan ulasan pada pesanan #{$review->order?->order_number} (Alasan: {$reason})",
            'changes_json'   => [
                'review_id'  => $review->id,
                'old_status' => $oldStatus,
                'new_status' => Review::STATUS_HIDDEN,
            ],
            'status'         => 'success',
            'ip_address'     => $request->ip(),
        ]);

        return back()->with('success', "Ulasan pesanan #{$review->order?->order_number} berhasil disembunyikan dari publik.");
    }
}
