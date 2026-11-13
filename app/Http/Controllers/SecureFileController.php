<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\ReturnEvidence;
use App\Models\ReturnRefund;
use App\Models\ReturnShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SecureFileController extends Controller
{
    /**
     * Menyajikan berkas bukti privat hanya untuk pengguna terotentikasi dan berwenang.
     * Mencegah akses publik dan path traversal.
     */
    public function show(Request $request, string $type, int|string $id, string $filename): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan masuk ke akun Anda untuk melihat berkas bukti.');
        }

        // Sanitasi nama berkas untuk mencegah path traversal (LFI)
        $cleanFilename = basename($filename);
        $path = null;
        $authorized = false;

        switch ($type) {
            case 'shipping_proof':
                $order = Order::findOrFail($id);
                $authorized = $user->isAdmin() || ($order->user_id === $user->id);
                $path = $order->shipping_proof_path;
                break;

            case 'delivery_proof':
                $order = Order::findOrFail($id);
                $authorized = $user->isAdmin() || ($order->user_id === $user->id);
                $path = $order->delivery_proof_path;
                break;

            case 'return_evidence':
                $evidence = ReturnEvidence::with('orderReturn')->findOrFail($id);
                $authorized = $user->isAdmin() || ($evidence->orderReturn && $evidence->orderReturn->user_id === $user->id);
                $path = $evidence->file_path;
                break;

            case 'return_shipment':
                $shipment = ReturnShipment::with('orderReturn')->findOrFail($id);
                $authorized = $user->isAdmin() || ($shipment->orderReturn && $shipment->orderReturn->user_id === $user->id);
                // Menentukan apakah file shipping_proof_path atau received_proof_path
                $path = str_contains($shipment->shipping_proof_path ?? '', $cleanFilename)
                    ? $shipment->shipping_proof_path
                    : $shipment->received_proof_path;
                break;

            case 'refund_proof':
                $refund = ReturnRefund::with('orderReturn')->findOrFail($id);
                $authorized = $user->isAdmin() || ($refund->orderReturn && $refund->orderReturn->user_id === $user->id);
                $path = $refund->proof_path;
                break;

            default:
                abort(404, 'Tipe berkas tidak dikenali.');
        }

        if (! $authorized) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat berkas ini.');
        }

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404, 'Berkas bukti tidak ditemukan.');
        }

        return Storage::disk('local')->response($path);
    }
}

