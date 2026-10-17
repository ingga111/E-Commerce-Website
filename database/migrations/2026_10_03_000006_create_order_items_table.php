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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variant_id')->constrained('product_variants');

            // Historical snapshot
            $table->string('product_name');
            $table->string('variant_name');
            $table->decimal('price', 14, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 14, 2);

            // Delivered credentials for Account type (securely displayed after payment)
            $table->text('credentials_delivered')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
