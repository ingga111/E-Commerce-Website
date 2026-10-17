<?php

namespace Database\Seeders;

use App\Models\FlashSale;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class PromoAndVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FlashSale::firstOrCreate(
            ['title' => 'FLASH SALE EVENT HARI INI'],
            [
                'subtitle' => 'Dapatkan harga diamond & pass termurah sebelum periode berakhir.',
                'badge_text' => 'HEMAT 30%',
                'discount_percentage' => 30,
                'start_time' => now(),
                'end_time' => now()->addHours(12),
                'target_url' => '#group-game',
                'is_active' => true,
            ]
        );

        Voucher::firstOrCreate(
            ['code' => 'HEMAT10'],
            [
                'name' => 'Diskon Member 10%',
                'description' => 'Diskon 10% untuk top up game dan pembelian akun favoritmu',
                'type' => 'percentage',
                'discount_amount' => 10,
                'min_order_amount' => 0,
                'max_discount' => 25000,
                'usage_limit' => 100,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ]
        );

        Voucher::firstOrCreate(
            ['code' => 'SULTAN5K'],
            [
                'name' => 'Potongan Langsung Rp 5.000',
                'description' => 'Potongan flat Rp 5.000 untuk transaksi minimal Rp 25.000',
                'type' => 'fixed',
                'discount_amount' => 5000,
                'min_order_amount' => 25000,
                'max_discount' => null,
                'usage_limit' => 50,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ]
        );
    }
}
