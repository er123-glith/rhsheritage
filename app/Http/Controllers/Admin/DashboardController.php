<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PartnerApplication;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'pendingOrdersCount' => Order::where('status', OrderStatus::MenungguKonfirmasi)->count(),
            'totalOrdersCount' => Order::count(),
            'totalCustomersCount' => User::where('role', 'customer')->where(function ($q) {
                $q->whereNull('is_owner')->orWhere('is_owner', false);
            })->count(),
            'pendingPartnersCount' => PartnerApplication::where('is_followed_up', false)->count(),
            'lowStockCount' => ProductVariant::lowStock()->count(),
            'recentOrders' => Order::with(['items', 'user'])->latest()->take(5)->get(),
        ]);
    }
}
