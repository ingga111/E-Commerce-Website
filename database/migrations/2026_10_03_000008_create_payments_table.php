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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Gateway info
            $table->string('payment_gateway')->default('tripay'); // tripay, midtrans, xendit
            $table->string('payment_method'); // QRIS, BRIVA, BCAVA, OVO, etc.
            $table->string('payment_name')->nullable(); // Nama Channel (e.g. QRIS (ShopeePay, Dana, dll))
            $table->string('reference')->nullable()->index(); // Gateway Transaction Reference
            $table->string('merchant_ref')->index(); // Order Number (INV-...)

            // Payment instructions / payload from Gateway
            $table->string('pay_code')->nullable(); // VA number or retail payment code
            $table->text('qr_url')->nullable(); // QR code image URL / raw string
            $table->text('checkout_url')->nullable(); // Gateway hosted checkout URL

            // Financials
            $table->decimal('amount', 14, 2);
            $table->decimal('fee', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2);

            // Status
            $table->string('status')->default('pending')->index(); // pending, paid, failed, expired
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Audit Log
            $table->json('raw_response')->nullable();
            $table->json('raw_callback')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
