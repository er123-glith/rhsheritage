<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_applications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('applicant_name', 120);
            $table->string('institution_name', 150)->nullable();
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('region', 120);
            $table->string('product_interest', 255);
            $table->string('estimated_quantity', 100);
            $table->text('message')->nullable();
            $table->boolean('is_followed_up')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_applications');
    }
};
