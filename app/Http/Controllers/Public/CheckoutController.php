<?php

namespace App\Http\Controllers\Public;

use App\Exceptions\EmptyCartException;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\ShippingZone;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PlaceOrderData;
use App\Services\ShippingCalculator;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(CartService $cart, ShippingCalculator $shipping)
    {
        $summary = $cart->summary();

        if ($summary->items->isEmpty()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Keranjang belanja Anda kosong. Silakan pilih produk terlebih dahulu.');
        }

        $zones = ShippingZone::where('is_active', true)->orderBy('sort_order')->get();

        return view('public.checkout.index', compact('summary', 'zones'));
    }

    /**
     * Kalkulasi estimasi ongkir secara real-time (AJAX preview).
     * Nilai ini hanya estimasi — harga final dihitung ulang di server saat store().
     */
    public function shipping(Request $request, CartService $cart, ShippingCalculator $shipping)
    {
        $request->validate(['zone_id' => ['required', 'exists:shipping_zones,id']]);

        $zone = ShippingZone::findOrFail($request->zone_id);
        $items = $cart->items();

        $cost = $shipping->calculate($items, $zone);
        $subtotal = $items->sum(fn($item) => $item->subtotal());

        return response()->json([
            'shipping_cost' => $cost,
            'subtotal'      => $subtotal,
            'total'         => $subtotal + $cost,
            'zone_name'     => $zone->name,
        ]);
    }

    public function store(PlaceOrderRequest $request, OrderService $orders, CartService $cart)
    {
        // Kumpulkan item keranjang saat ini [variantId => qty]
        $cartItems = $cart->items()
            ->mapWithKeys(fn($item) => [$item->variantId => $item->quantity])
            ->toArray();

        try {
            $data = PlaceOrderData::fromRequest($request, $cartItems);
            $order = $orders->place($data);

            // Kosongkan keranjang setelah pesanan berhasil disimpan
            $cart->clear();

            return redirect()->route('order.confirmation', $order->order_number)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (EmptyCartException) {
            return redirect()->route('catalog.index')
                ->with('error', 'Keranjang belanja kosong. Silakan pilih produk terlebih dahulu.');

        } catch (InsufficientStockException $e) {
            return back()
                ->withInput()
                ->with('error', $e->userMessage());
        }
    }
}
