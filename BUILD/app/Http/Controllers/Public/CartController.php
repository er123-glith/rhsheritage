<?php

namespace App\Http\Controllers\Public;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cart)
    {
        $summary = $cart->summary();
        return view('public.cart.index', compact('summary'));
    }

    public function store(StoreCartItemRequest $request, CartService $cart)
    {
        try {
            $cart->add(
                (int) $request->validated('variant_id'),
                (int) $request->validated('quantity')
            );

            return back()
                ->with('success', 'Produk berhasil ditambahkan ke keranjang.');

        } catch (InsufficientStockException $e) {
            return back()
                ->withErrors(['quantity' => $e->userMessage()])
                ->with('error', $e->userMessage());
        }
    }

    public function update(Request $request, int $variantId, CartService $cart)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $cart->update($variantId, (int) $request->quantity);

            if ($request->wantsJson()) {
                $summary = $cart->summary();
                return response()->json([
                    'success'   => true,
                    'count'     => $cart->count(),
                    'subtotal'  => $summary->subtotal,
                ]);
            }

            return back()->with('success', 'Jumlah berhasil diperbarui.');

        } catch (InsufficientStockException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->userMessage()], 422);
            }
            return back()->with('error', $e->userMessage());
        }
    }

    public function destroy(int $variantId, CartService $cart)
    {
        $cart->remove($variantId);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'count' => $cart->count()]);
        }

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
