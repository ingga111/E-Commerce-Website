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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. QRIS, BCAVA, BRIVA, MANDIRIVA, OVO, DANA
            $table->string('name'); // e.g. BCA Virtual Account
            $table->string('group')->default('Virtual Account'); // QRIS, Virtual Account, E-Wallet, Transfer Manual
            $table->decimal('fee_flat', 12, 2)->default(1000);
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->text('instructions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
