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
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('group');
            $table->string('account_holder')->nullable()->after('account_number');
            $table->string('qr_image')->nullable()->after('account_holder');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('pay_code');
            $table->string('account_holder')->nullable()->after('account_number');
            $table->string('payment_proof')->nullable()->after('checkout_url');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->text('admin_note')->nullable()->after('payment_proof_uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'account_holder', 'qr_image']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'account_number',
                'account_holder',
                'payment_proof',
                'payment_proof_uploaded_at',
                'admin_note',
            ]);
        });
    }
};
