<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 20);
            $table->text('address_line');
            $table->string('city', 100);
            $table->string('province', 100);
            $table->string('postal_code', 10);
            $table->text('note')->nullable();
            $table->foreignId('shipping_zone_id')->nullable()->constrained('shipping_zones')->onDelete('set null');
            $table->string('shipping_zone_name', 100);
            $table->unsignedInteger('total_weight_gram');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('status', 30);
            $table->text('cancellation_reason')->nullable();
            $table->string('tracking_number', 60)->nullable();
            $table->json('payload_json')->nullable();
            $table->timestamp('sent_to_wa_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['order_number', 'customer_phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
