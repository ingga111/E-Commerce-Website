<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('fixed'); // 'fixed' or 'percentage'
            $table->decimal('discount_amount', 14, 2);
            $table->decimal('min_order_amount', 14, 2)->default(0);
            $table->decimal('max_discount', 14, 2)->nullable(); // Max cap if percentage
            $table->unsignedInteger('usage_limit')->nullable(); // Null = unlimited
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
