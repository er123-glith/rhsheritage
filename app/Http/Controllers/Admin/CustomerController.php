<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar pelanggan (role = customer).
     * Akun admin tidak ditampilkan di sini (Section C & E).
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->where(function ($q) {
                $q->whereNull('is_owner')->orWhere('is_owner', false);
            })
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('status', OrderStatus::Selesai);
            }], 'total');

        // Pencarian berdasarkan nama dan email
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();
        $totalCustomers = User::where('role', 'customer')
            ->where(function ($q) {
                $q->whereNull('is_owner')->orWhere('is_owner', false);
            })->count();

        return view('admin.customers.index', compact('customers', 'totalCustomers'));
    }

    /**
     * Menampilkan profil detail pelanggan beserta riwayat pesanan dan statistik belanja.
     */
    public function show(User $customer): View
    {
        // Proteksi: cegah akses profil admin melalui rute pelanggan
        if ($customer->isAdmin()) {
            abort(404, 'Data pelanggan tidak ditemukan.');
        }

        $customer->loadCount('orders');

        $totalSpent = (float) $customer->orders()
            ->where('status', OrderStatus::Selesai)
            ->sum('total');

        $completedOrdersCount = $customer->orders()
            ->where('status', OrderStatus::Selesai)
            ->count();

        $pendingOrdersCount = $customer->orders()
            ->where('status', OrderStatus::MenungguKonfirmasi)
            ->count();

        $orders = $customer->orders()
            ->with(['items', 'shippingZone'])
            ->latest()
            ->paginate(10);

        return view('admin.customers.show', compact(
            'customer',
            'totalSpent',
            'completedOrdersCount',
            'pendingOrdersCount',
            'orders'
        ));
    }
}

