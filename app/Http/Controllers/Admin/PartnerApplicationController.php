<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Models\PartnerApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerApplicationController extends Controller
{
    public function index(Request $request): View
    {
        // Hitung statistik pengajuan
        $totalCount = PartnerApplication::count();
        $pendingCount = PartnerApplication::where('is_followed_up', false)->count();
        $followedUpCount = PartnerApplication::where('is_followed_up', true)->count();
        $wholesaleCount = PartnerApplication::where('type', ApplicationType::Grosir)->count();
        $partnerCount = PartnerApplication::where('type', ApplicationType::Mitra)->count();

        $query = PartnerApplication::orderBy('id', 'desc');

        // Filter Tipe (grosir / mitra)
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Filter Status Tindak Lanjut
        if ($status = $request->input('status')) {
            if ($status === 'pending') {
                $query->where('is_followed_up', false);
            } elseif ($status === 'followed_up') {
                $query->where('is_followed_up', true);
            }
        }

        // Filter Pencarian
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                    ->orWhere('institution_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('product_interest', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('admin.partners.index', compact(
            'applications',
            'totalCount',
            'pendingCount',
            'followedUpCount',
            'wholesaleCount',
            'partnerCount'
        ));
    }

    public function toggleFollowup(PartnerApplication $partner): RedirectResponse
    {
        $partner->update([
            'is_followed_up' => ! $partner->is_followed_up,
        ]);

        $statusText = $partner->is_followed_up ? 'sudah ditindaklanjuti' : 'belum ditindaklanjuti';

        return back()->with('status', "Pengajuan dari \"{$partner->applicant_name}\" ditandai {$statusText}.");
    }

    public function destroy(PartnerApplication $partner): RedirectResponse
    {
        $name = $partner->applicant_name;
        $partner->delete();

        return back()->with('status', "Pengajuan kemitraan dari \"{$name}\" berhasil dihapus.");
    }
}
