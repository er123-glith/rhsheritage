<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar pelanggan (role = customer).
     * Akun admin tidak ditampilkan di sini (Section C & E).
     * Mendukung pencarian, filter status akun, dan pengurutan terbaru.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status', 'all');

        $query = User::where('role', 'customer')
            ->where(function ($q) {
                $q->whereNull('is_owner')->orWhere('is_owner', false);
            })
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('status', OrderStatus::Selesai);
            }], 'total');

        // Filter status akun: active (hanya aktif), trashed (hanya nonaktif/terhapus), all (semua)
        if ($statusFilter === 'active') {
            $query->whereNull('deleted_at');
        } elseif ($statusFilter === 'inactive' || $statusFilter === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->withTrashed();
        }

        // Pencarian berdasarkan nama, email, atau nomor telepon
        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest('id')->paginate(15)->withQueryString();

        // Hitung statistik untuk filter badge
        $totalCustomers = User::withTrashed()->where('role', 'customer')->where(function ($q) {
            $q->whereNull('is_owner')->orWhere('is_owner', false);
        })->count();

        $activeCustomersCount = User::where('role', 'customer')->where(function ($q) {
            $q->whereNull('is_owner')->orWhere('is_owner', false);
        })->count();

        $inactiveCustomersCount = User::onlyTrashed()->where('role', 'customer')->where(function ($q) {
            $q->whereNull('is_owner')->orWhere('is_owner', false);
        })->count();

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'activeCustomersCount',
            'inactiveCustomersCount',
            'statusFilter'
        ));
    }

    /**
     * Menampilkan profil detail pelanggan beserta riwayat pesanan dan statistik belanja.
     */
    public function show(int|string $id): View
    {
        $customer = User::withTrashed()->findOrFail($id);

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

    /**
     * Menampilkan formulir edit profil pelanggan.
     */
    public function edit(int|string $id): View
    {
        $customer = User::withTrashed()->findOrFail($id);

        if ($customer->isAdmin()) {
            abort(404, 'Data pelanggan tidak ditemukan.');
        }

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Memperbarui data profil pelanggan dengan whitelist atribut aman.
     */
    public function update(UpdateCustomerRequest $request, int|string $id): RedirectResponse
    {
        $customer = User::withTrashed()->findOrFail($id);

        if ($customer->isAdmin()) {
            abort(403, 'Akses ditolak. Tidak dapat memodifikasi akun admin melalui modul pelanggan.');
        }

        $validated = $request->validated();
        $oldData = $customer->only(['name', 'email', 'phone']);

        $customer->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Catat aktivitas log admin
        AdminActivityLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $customer->id,
            'action'         => 'update',
            'description'    => "Memperbarui data profil pelanggan '{$customer->name}' (#{$customer->id})",
            'changes_json'   => [
                'before' => $oldData,
                'after'  => $customer->only(['name', 'email', 'phone']),
            ],
            'status'         => 'success',
            'ip_address'     => $request->ip(),
        ]);

        Log::info("Admin [#" . auth()->id() . "] updated customer [#" . $customer->id . "]", [
            'admin_id'    => auth()->id(),
            'customer_id' => $customer->id,
            'changes'     => $customer->getChanges(),
        ]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('status', "Data pelanggan '{$customer->name}' berhasil diperbarui.");
    }

    /**
     * Menonaktifkan akun pelanggan menggunakan soft delete.
     * Riwayat pesanan dan relasi data tetap dipertahankan.
     */
    public function destroy(DestroyCustomerRequest $request, int|string $id): RedirectResponse
    {
        $customer = User::withTrashed()->findOrFail($id);

        // Proteksi: Admin tidak boleh menghapus dirinya sendiri
        if ($customer->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Proteksi: Akun admin tidak boleh dihapus melalui endpoint customer
        if ($customer->isAdmin()) {
            abort(403, 'Akun administrator tidak dapat dinonaktifkan.');
        }

        // Proteksi: Cek apakah akun sudah dinonaktifkan sebelumnya
        if ($customer->trashed()) {
            return back()->with('error', 'Akun pelanggan ini sudah berstatus nonaktif.');
        }

        $customerName = $customer->name;
        $customerId = $customer->id;

        $customer->delete();

        // Catat aktivitas log admin
        AdminActivityLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $customerId,
            'action'         => 'delete',
            'description'    => "Menonaktifkan (soft delete) akun pelanggan '{$customerName}' (#{$customerId})",
            'changes_json'   => [
                'deleted_at' => now()->toDateTimeString(),
            ],
            'status'         => 'success',
            'ip_address'     => $request->ip(),
        ]);

        Log::warning("Admin [#" . auth()->id() . "] soft-deleted customer [#" . $customerId . "]", [
            'admin_id'    => auth()->id(),
            'customer_id' => $customerId,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('status', "Akun pelanggan '{$customerName}' berhasil dinonaktifkan. Seluruh riwayat transaksi tetap dipertahankan.");
    }
}
