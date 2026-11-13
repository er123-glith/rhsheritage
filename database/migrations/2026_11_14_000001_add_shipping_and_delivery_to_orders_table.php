<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan informasi ekspedisi, resi J&T, bukti pengiriman, dan bukti penerimaan customer.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_name', 50)->default('J&T Express')->after('tracking_number');
            $table->dateTime('shipping_date')->nullable()->after('courier_name');
            $table->string('shipping_proof_path')->nullable()->after('shipping_date');
            $table->text('shipping_note')->nullable()->after('shipping_proof_path');
            $table->dateTime('delivered_at')->nullable()->after('shipping_note');
            $table->string('delivery_proof_path')->nullable()->after('delivered_at');
            $table->foreignId('delivery_confirmed_by')->nullable()->after('delivery_proof_path')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_confirmed_by']);
            $table->dropColumn([
                'courier_name',
                'shipping_date',
                'shipping_proof_path',
                'shipping_note',
                'delivered_at',
                'delivery_proof_path',
                'delivery_confirmed_by',
            ]);
        });
    }
};

