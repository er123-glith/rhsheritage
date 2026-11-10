<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingZoneRequest;
use App\Http\Requests\Admin\UpdateShippingZoneRequest;
use App\Models\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ShippingZoneController extends Controller
{
    public function index(): View
    {
        $zones = ShippingZone::withCount('orders')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.shipping.index', compact('zones'));
    }

    public function create(): View
    {
        $nextOrder = (ShippingZone::max('sort_order') ?? 0) + 1;

        return view('admin.shipping.create', compact('nextOrder'));
    }

    public function store(StoreShippingZoneRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        ShippingZone::create($data);

        Cache::forget('shipping_zones');

        return redirect()->route('admin.shipping.index')->with('status', 'Zona pengiriman baru berhasil ditambahkan.');
    }

    public function edit(ShippingZone $shippingZone): View
    {
        return view('admin.shipping.edit', ['zone' => $shippingZone]);
    }

    public function update(UpdateShippingZoneRequest $request, ShippingZone $shippingZone): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $shippingZone->update($data);

        Cache::forget('shipping_zones');

        return redirect()->route('admin.shipping.index')->with('status', "Tarif zona \"{$shippingZone->name}\" berhasil diperbarui.");
    }

    public function toggleActive(ShippingZone $shippingZone): RedirectResponse
    {
        $shippingZone->update([
            'is_active' => ! $shippingZone->is_active,
        ]);

        Cache::forget('shipping_zones');

        $statusText = $shippingZone->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('status', "Zona \"{$shippingZone->name}\" berhasil {$statusText}.");
    }

    public function destroy(ShippingZone $shippingZone): RedirectResponse
    {
        if ($shippingZone->orders()->exists()) {
            return back()->with('error', "Zona \"{$shippingZone->name}\" tidak dapat dihapus karena sudah memiliki riwayat pesanan terkait. Silakan nonaktifkan zona ini agar tidak muncul pada form pemesanan pelanggan.");
        }

        $name = $shippingZone->name;
        $shippingZone->delete();

        Cache::forget('shipping_zones');

        return redirect()->route('admin.shipping.index')->with('status', "Zona \"{$name}\" berhasil dihapus.");
    }
}
