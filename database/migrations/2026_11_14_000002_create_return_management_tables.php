<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel-tabel terintegrasi untuk Return Management System (RMS).
     */
    public function up(): void
    {
        // 1. Tabel utama pengajuan retur
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 30)->unique();
            $table->foreignId('order_id')->constrained('orders')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('solution', 20); // 'replacement' atau 'refund'
            $table->string('reason', 50);   // 'rusak_cacat', 'tidak_sesuai', 'salah_varian', 'jumlah_kurang', 'tidak_lengkap', 'lainnya'
            $table->text('description');
            $table->string('status', 30)->default('menunggu_review');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('agreed_terms')->default(true);
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // 2. Tabel item produk yang diretur
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('restrict');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->index(['return_id', 'order_item_id']);
        });

        // 3. Tabel bukti berkas retur (video unboxing wajib & foto bukti)
        Schema::create('return_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->string('file_type', 30); // 'unboxing_video', 'photo', 'received_proof'
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();

            $table->index(['return_id', 'file_type']);
        });

        // 4. Tabel pengiriman retur & penggantian barang (J&T Express)
        Schema::create('return_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->string('type', 40); // 'return_from_customer', 'replacement_from_admin'
            $table->string('courier', 50)->default('J&T Express');
            $table->string('tracking_number', 60);
            $table->dateTime('shipped_at')->nullable();
            $table->string('shipping_proof_path')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->string('received_proof_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['return_id', 'type']);
        });

        // 5. Tabel manajemen pengembalian dana (refund)
        Schema::create('return_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('bank_name', 60)->nullable();
            $table->string('account_number', 60)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->string('status', 30)->default('belum_diproses'); // 'belum_diproses', 'diproses', 'menunggu_konfirmasi', 'diterima', 'gagal'
            $table->string('payment_reference', 100)->nullable();
            $table->string('proof_path')->nullable();
            $table->dateTime('transferred_at')->nullable();
            $table->dateTime('confirmed_received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['return_id', 'status']);
        });

        // 6. Tabel riwayat status retur (audit trail)
        Schema::create('return_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->string('actor_type', 20)->default('system'); // 'admin', 'customer', 'system'
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['return_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_status_histories');
        Schema::dropIfExists('return_refunds');
        Schema::dropIfExists('return_shipments');
        Schema::dropIfExists('return_evidences');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
    }
};
