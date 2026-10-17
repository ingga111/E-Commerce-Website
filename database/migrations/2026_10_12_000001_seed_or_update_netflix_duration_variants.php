<?php

use App\Enums\ProductType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find or create account category
        $category = Category::where('slug', 'akun-premium')->first();
        if (! $category) {
            $category = Category::create([
                'slug' => 'akun-premium',
                'name' => 'Akun Premium & Streaming',
                'type' => 'account',
                'icon' => 'tv',
                'sort_order' => 2,
                'is_active' => true,
            ]);
        }

        // Find or create Netflix product (restore if soft-deleted)
        $netflix = Product::withTrashed()->where('slug', 'netflix-premium')->first();
        if ($netflix) {
            if ($netflix->trashed()) {
                $netflix->restore();
            }
            $netflix->update([
                'category_id' => $category->id,
                'name' => 'Netflix Premium UHD 4K',
                'type' => ProductType::Account,
                'publisher' => 'Netflix, Inc.',
                'requires_user_id' => false,
                'requires_zone_id' => false,
                'description' => 'Akun Netflix Premium kualitas Ultra HD 4K, anti screen limit, dan garansi aktif sesuai paket.',
                'instructions' => 'Kredensial akun (Email, Password & PIN Profil) akan dikirimkan otomatis setelah pembayaran berhasil.',
                'sort_order' => 3,
                'is_active' => true,
            ]);
        } else {
            $netflix = Product::create([
                'slug' => 'netflix-premium',
                'category_id' => $category->id,
                'name' => 'Netflix Premium UHD 4K',
                'type' => ProductType::Account,
                'publisher' => 'Netflix, Inc.',
                'requires_user_id' => false,
                'requires_zone_id' => false,
                'description' => 'Akun Netflix Premium kualitas Ultra HD 4K, anti screen limit, dan garansi aktif sesuai paket.',
                'instructions' => 'Kredensial akun (Email, Password & PIN Profil) akan dikirimkan otomatis setelah pembayaran berhasil.',
                'sort_order' => 3,
                'is_active' => true,
            ]);
        }

        $variants = [
            [
                'sku' => 'NF-1D-SHR',
                'name' => '1 Hari - 1 Profil (Sharing)',
                'price' => 5000,
                'cost_price' => 3000,
                'stock_type' => 'manual',
                'stock' => 20,
                'sort_order' => 1,
            ],
            [
                'sku' => 'NF-3D-SHR',
                'name' => '3 Hari - 1 Profil (Sharing)',
                'price' => 12000,
                'cost_price' => 8000,
                'stock_type' => 'manual',
                'stock' => 15,
                'sort_order' => 2,
            ],
            [
                'sku' => 'NF-7D-SHR',
                'name' => '7 Hari - 1 Profil (Sharing)',
                'price' => 22000,
                'cost_price' => 15000,
                'stock_type' => 'manual',
                'stock' => 15,
                'sort_order' => 3,
            ],
            [
                'sku' => 'NF-1M-SHR',
                'name' => '1 Bulan - 1 Profil (Sharing)',
                'price' => 35000,
                'cost_price' => 25000,
                'stock_type' => 'manual',
                'stock' => 10,
                'sort_order' => 4,
            ],
            [
                'sku' => 'NF-1M-PRV',
                'name' => '1 Bulan - Akun Private (5 Profil)',
                'price' => 135000,
                'cost_price' => 110000,
                'stock_type' => 'manual',
                'stock' => 5,
                'sort_order' => 5,
            ],
        ];

        foreach ($variants as $var) {
            ProductVariant::withTrashed()->updateOrCreate(
                ['sku' => $var['sku']],
                [
                    'product_id' => $netflix->id,
                    'name' => $var['name'],
                    'price' => $var['price'],
                    'cost_price' => $var['cost_price'],
                    'stock_type' => $var['stock_type'],
                    'stock' => $var['stock'],
                    'sort_order' => $var['sort_order'],
                    'is_active' => true,
                    'deleted_at' => null,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ProductVariant::whereIn('sku', ['NF-1D-SHR', 'NF-3D-SHR', 'NF-7D-SHR'])->delete();
    }
};
