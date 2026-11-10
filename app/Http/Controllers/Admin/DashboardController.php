<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PartnerApplication;
use App\Models\ProductVariant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'pendingOrdersCount' => Order::where('status', OrderStatus::MenungguKonfirmasi)->count(),
            'pendingPartnersCount' => PartnerApplication::where('is_followed_up', false)->count(),
            'lowStockCount' => ProductVariant::lowStock()->count(),
        ]);
    }
}
