<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminResetCustomerPasswordRequest;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerPasswordController extends Controller
{
    /**
     * Menampilkan formulir ganti kata sandi untuk pelanggan tertentu.
     */
    public function edit(int|string $id): View|RedirectResponse
    {
        $customer = User::withTrashed()->findOrFail($id);

        // Proteksi: Tidak dapat mengganti password akun admin via modul customer
        if ($customer->isAdmin()) {
            abort(403, 'Akses ditolak. Tidak dapat memodifikasi akun administrator melalui modul pelanggan.');
        }

        // Proteksi: Akun pelanggan yang dinonaktifkan tidak dapat diganti kata sandinya
        if ($customer->trashed()) {
            return redirect()->route('admin.customers.show', $customer)
                ->with('error', 'Tidak dapat mengubah kata sandi akun pelanggan yang sedang berstatus nonaktif.');
        }

        return view('admin.customers.password', compact('customer'));
    }

    /**
     * Memproses penggantian kata sandi pelanggan oleh Administrator dengan verifikasi ketat.
     */
    public function update(AdminResetCustomerPasswordRequest $request, int|string $id): RedirectResponse
    {
        $admin = $request->user();
        $customer = User::withTrashed()->findOrFail($id);

        // 1. Proteksi: Tidak dapat memodifikasi akun admin lain
        if ($customer->isAdmin()) {
            abort(403, 'Akses ditolak. Tidak dapat memodifikasi akun administrator melalui modul pelanggan.');
        }

        // 2. Proteksi: Akun nonaktif (soft-deleted) tidak dapat diganti kata sandinya
        if ($customer->trashed()) {
            return redirect()->route('admin.customers.show', $customer)
                ->with('error', 'Tidak dapat mengubah kata sandi akun pelanggan yang sedang berstatus nonaktif.');
        }

        // 3. Verifikasi Keamanan: Password admin pelaksana wajib valid
        if (! Hash::check($request->input('admin_password'), $admin->password)) {
            return back()
                ->withErrors([
                    'admin_password' => 'Kata sandi verifikasi Administrator tidak valid.',
                ])
                ->withInput($request->except(['password', 'password_confirmation', 'admin_password']));
        }

        // 4. Validasi Kebijakan: Kata sandi baru tidak boleh sama dengan kata sandi pelanggan saat ini
        if (Hash::check($request->input('password'), $customer->password)) {
            return back()
                ->withErrors([
                    'password' => 'Kata sandi baru tidak boleh sama dengan kata sandi pelanggan saat ini.',
                ])
                ->withInput($request->except(['password', 'password_confirmation', 'admin_password']));
        }

        // 5. Update kata sandi baru (ter-hash) & rotasi remember_token
        $customer->forceFill([
            'password'       => Hash::make($request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        // 6. Pencabutan Sesi: Hapus seluruh sesi aktif milik customer dari tabel sessions
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')
                ->where('user_id', $customer->id)
                ->delete();
        }

        // 7. Catat aktivitas ke tabel audit log admin (tanpa menyimpan plaintext atau hash)
        AdminActivityLog::create([
            'admin_id'       => $admin->id,
            'target_user_id' => $customer->id,
            'action'         => 'password_changed_by_admin',
            'description'    => "Mereset kata sandi akun pelanggan '{$customer->name}' (#{$customer->id})",
            'changes_json'   => [
                'password_reset_at' => now()->toDateTimeString(),
            ],
            'status'         => 'success',
            'ip_address'     => $request->ip(),
        ]);

        // 8. Catat log operasional Laravel
        Log::info("Admin [#{$admin->id}] reset password for customer [#{$customer->id}]", [
            'admin_id'    => $admin->id,
            'customer_id' => $customer->id,
            'ip'          => $request->ip(),
        ]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('status', "Kata sandi untuk pelanggan '{$customer->name}' berhasil diperbarui.");
    }
}
