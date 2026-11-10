<?php

namespace App\Http\Controllers\Public;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerApplicationRequest;
use App\Models\PageContent;
use App\Models\PartnerApplication;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    public function index(Request $request)
    {
        $grosirContent = PageContent::where('key', 'grosir')->first();
        $mitraContent  = PageContent::where('key', 'mitra')->first();

        $activeTab = $request->get('tab', 'grosir');

        return view('public.partnership.index', compact('grosirContent', 'mitraContent', 'activeTab'));
    }

    public function store(PartnerApplicationRequest $request)
    {
        // Honeypot check (SEC-13) — sudah divalidasi di FormRequest (website max:0)
        // Jika sampai sini berarti honeypot kosong (valid)

        PartnerApplication::create([
            'type'               => ApplicationType::from($request->validated('type')),
            'applicant_name'     => $request->validated('applicant_name'),
            'institution_name'   => $request->validated('institution_name'),
            'phone'              => $request->validated('phone'),
            'email'              => $request->validated('email'),
            'region'             => $request->validated('region'),
            'product_interest'   => $request->validated('product_interest'),
            'estimated_quantity' => $request->validated('estimated_quantity'),
            'message'            => $request->validated('message'),
            'is_followed_up'     => false,
        ]);

        return back()->with('success', 'Pengajuan Anda berhasil dikirim! Kami akan menghubungi Anda melalui WhatsApp atau Email dalam 1-2 hari kerja.');
    }
}
