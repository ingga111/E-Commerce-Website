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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->index(); // e.g. "INV-20261003-8941"
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Customer Contact
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // Product Category / Target fulfillment
            $table->string('product_type'); // 'game' or 'account'
            $table->string('target_user_id')->nullable(); // Game ID or Target Account User
            $table->string('target_zone_id')->nullable(); // Server / Zone ID
            $table->string('target_nickname')->nullable(); // In-game nickname verified

            // Financials
            $table->decimal('subtotal', 14, 2);
            $table->decimal('admin_fee', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2);

            // Order Status
            $table->string('status')->default('pending')->index(); // pending, processing, completed, failed, expired, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
