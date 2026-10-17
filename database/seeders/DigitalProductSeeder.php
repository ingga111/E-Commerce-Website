<?php

namespace Database\Seeders;

use App\Enums\ProductType;
use App\Enums\UserRole;
use App\Models\AccountStock;
use App\Models\Banner;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DigitalProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Users
        User::updateOrCreate(
            ['email' => 'admin@bumhhimatika.biz.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('Himatika123@'),
                'role' => UserRole::Admin,
                'phone' => '081234567890',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => UserRole::Customer,
                'phone' => '089876543210',
            ]
        );

        // 2. Seed Categories
        $gameCategory = Category::updateOrCreate(
            ['slug' => 'topup-game'],
            [
                'name' => 'Top-Up Game',
                'type' => 'game',
                'icon' => 'gamepad',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $accountCategory = Category::updateOrCreate(
            ['slug' => 'akun-premium'],
            [
                'name' => 'Akun Premium & Streaming',
                'type' => 'account',
                'icon' => 'tv',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $gameAccountCategory = Category::updateOrCreate(
            ['slug' => 'jual-beli-akun-game'],
            [
                'name' => 'Jual Beli Akun Game',
                'type' => 'game_account',
                'icon' => 'user-check',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $jokiCategory = Category::updateOrCreate(
            ['slug' => 'jasa-joki-rank'],
            [
                'name' => 'Jasa Joki Rank & Winrate',
                'type' => 'joki',
                'icon' => 'shield',
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        // 3. Seed Products - Game: Mobile Legends
        $mlbb = Product::updateOrCreate(
            ['slug' => 'mobile-legends'],
            [
                'category_id' => $gameCategory->id,
                'name' => 'Mobile Legends: Bang Bang',
                'type' => ProductType::Game,
                'publisher' => 'Moonton',
                'requires_user_id' => true,
                'user_id_label' => 'User ID',
                'requires_zone_id' => true,
                'zone_id_label' => 'Zone ID (4-5 Digit)',
                'description' => 'Top up diamond Mobile Legends resmi dan terpercaya. Masukkan User ID dan Zone ID Anda untuk pengisian otomatis.',
                'instructions' => 'Contoh: User ID: 12345678, Zone ID: 2024. Anda dapat menemukan ID di profil akun MLBB Anda.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $mlVariants = [
            ['name' => '86 Diamonds', 'sku' => 'ML-86', 'price' => 21000, 'cost_price' => 19500, 'sort_order' => 1],
            ['name' => '172 Diamonds', 'sku' => 'ML-172', 'price' => 41500, 'cost_price' => 38000, 'sort_order' => 2],
            ['name' => '257 Diamonds', 'sku' => 'ML-257', 'price' => 62000, 'cost_price' => 57000, 'sort_order' => 3],
            ['name' => 'Weekly Diamond Pass', 'sku' => 'ML-WDP', 'price' => 27500, 'cost_price' => 25000, 'sort_order' => 4],
        ];

        foreach ($mlVariants as $var) {
            ProductVariant::updateOrCreate(
                ['sku' => $var['sku']],
                [
                    'product_id' => $mlbb->id,
                    'name' => $var['name'],
                    'price' => $var['price'],
                    'cost_price' => $var['cost_price'],
                    'stock_type' => 'unlimited',
                    'sort_order' => $var['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Seed Products - Game: Free Fire
        $ff = Product::updateOrCreate(
            ['slug' => 'free-fire'],
            [
                'category_id' => $gameCategory->id,
                'name' => 'Free Fire',
                'type' => ProductType::Game,
                'publisher' => 'Garena',
                'requires_user_id' => true,
                'user_id_label' => 'Player ID',
                'requires_zone_id' => false,
                'description' => 'Beli Diamond Free Fire instan dan murah.',
                'instructions' => 'Masukkan Player ID Free Fire Anda yang tertera pada kartu profil.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $ffVariants = [
            ['name' => '70 Diamonds', 'sku' => 'FF-70', 'price' => 9500, 'cost_price' => 8800, 'sort_order' => 1],
            ['name' => '140 Diamonds', 'sku' => 'FF-140', 'price' => 18900, 'cost_price' => 17500, 'sort_order' => 2],
            ['name' => '355 Diamonds', 'sku' => 'FF-355', 'price' => 46500, 'cost_price' => 43000, 'sort_order' => 3],
        ];

        foreach ($ffVariants as $var) {
            ProductVariant::updateOrCreate(
                ['sku' => $var['sku']],
                [
                    'product_id' => $ff->id,
                    'name' => $var['name'],
                    'price' => $var['price'],
                    'cost_price' => $var['cost_price'],
                    'stock_type' => 'unlimited',
                    'sort_order' => $var['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Seed Products - Account: Netflix Premium
        $netflix = Product::updateOrCreate(
            ['slug' => 'netflix-premium'],
            [
                'category_id' => $accountCategory->id,
                'name' => 'Netflix Premium UHD 4K',
                'type' => ProductType::Account,
                'publisher' => 'Netflix, Inc.',
                'requires_user_id' => false,
                'requires_zone_id' => false,
                'description' => 'Akun Netflix Premium kualitas Ultra HD 4K, anti screen limit, dan full garansi.',
                'instructions' => 'Kredensial akun (Email, Password & PIN Profil) akan dikirimkan langsung oleh Admin melalui WhatsApp setelah pembayaran berhasil dikonfirmasi.',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $nf1Day = ProductVariant::updateOrCreate(
            ['sku' => 'NF-1D-SHR'],
            [
                'product_id' => $netflix->id,
                'name' => '1 Hari - 1 Profil (Sharing)',
                'price' => 5000,
                'cost_price' => 3000,
                'stock_type' => 'manual',
                'stock' => 20,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $nf3Day = ProductVariant::updateOrCreate(
            ['sku' => 'NF-3D-SHR'],
            [
                'product_id' => $netflix->id,
                'name' => '3 Hari - 1 Profil (Sharing)',
                'price' => 12000,
                'cost_price' => 8000,
                'stock_type' => 'manual',
                'stock' => 15,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $nf7Day = ProductVariant::updateOrCreate(
            ['sku' => 'NF-7D-SHR'],
            [
                'product_id' => $netflix->id,
                'name' => '7 Hari - 1 Profil (Sharing)',
                'price' => 22000,
                'cost_price' => 15000,
                'stock_type' => 'manual',
                'stock' => 15,
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $nfSharing = ProductVariant::updateOrCreate(
            ['sku' => 'NF-1M-SHR'],
            [
                'product_id' => $netflix->id,
                'name' => '1 Bulan - 1 Profil (Sharing)',
                'price' => 35000,
                'cost_price' => 25000,
                'stock_type' => 'manual',
                'stock' => 10,
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        $nfPrivate = ProductVariant::updateOrCreate(
            ['sku' => 'NF-1M-PRV'],
            [
                'product_id' => $netflix->id,
                'name' => '1 Bulan - Akun Private (5 Profil)',
                'price' => 135000,
                'cost_price' => 110000,
                'stock_type' => 'manual',
                'stock' => 5,
                'sort_order' => 5,
                'is_active' => true,
            ]
        );

        // Preload Account Stocks for Netflix (1 Hari, 3 Hari, 7 Hari, 1 Bulan)
        $stocks = [
            [
                'product_variant_id' => $nf1Day->id,
                'credentials' => "Email: netflix.day01@streamhub.id\nPassword: DayPass#2026\nProfil: Profil 1\nPIN: 1122",
                'notes' => 'Garansi 1 hari aktif. Dilarang mengganti password atau mengutak-atik profil lain.',
            ],
            [
                'product_variant_id' => $nf3Day->id,
                'credentials' => "Email: netflix.day03@streamhub.id\nPassword: Quick3D#2026\nProfil: Profil 2\nPIN: 3344",
                'notes' => 'Garansi 3 hari aktif. Dilarang mengganti password.',
            ],
            [
                'product_variant_id' => $nf7Day->id,
                'credentials' => "Email: netflix.week01@streamhub.id\nPassword: Week7D#2026\nProfil: Profil 4\nPIN: 7788",
                'notes' => 'Garansi 7 hari aktif.',
            ],
            [
                'product_variant_id' => $nfSharing->id,
                'credentials' => "Email: stream.box01@gmail.com\nPassword: NetFlix#VIP2026\nProfil: Profil 2\nPIN: 4821",
                'notes' => 'Garansi 30 hari. Dilarang mengganti password atau mengotori profil pengguna lain.',
            ],
            [
                'product_variant_id' => $nfSharing->id,
                'credentials' => "Email: stream.box01@gmail.com\nPassword: NetFlix#VIP2026\nProfil: Profil 3\nPIN: 9102",
                'notes' => 'Garansi 30 hari. Dilarang mengganti password.',
            ],
            [
                'product_variant_id' => $nfPrivate->id,
                'credentials' => "Email: private.premium26@outlook.com\nPassword: FullAccount#Secret88\nPIN: Bebas set sendiri (Private)",
                'notes' => 'Full Private Account, bebas ganti password & profil.',
            ],
        ];

        foreach ($stocks as $st) {
            AccountStock::firstOrCreate(
                ['credentials' => $st['credentials']],
                [
                    'product_variant_id' => $st['product_variant_id'],
                    'notes' => $st['notes'],
                    'is_used' => false,
                ]
            );
        }

        // 6. Seed Products - Account: Spotify Premium
        $spotify = Product::updateOrCreate(
            ['slug' => 'spotify-premium'],
            [
                'category_id' => $accountCategory->id,
                'name' => 'Spotify Premium Individual',
                'type' => ProductType::Account,
                'publisher' => 'Spotify AB',
                'requires_user_id' => false,
                'requires_zone_id' => false,
                'description' => 'Dengarkan jutaan lagu tanpa jeda iklan, download offline, audio bitrate tinggi 320kbps.',
                'instructions' => 'Kredensial akun premium akan otomatis diberikan setelah pembayaran.',
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        $spotVariant = ProductVariant::updateOrCreate(
            ['sku' => 'SPOT-1M'],
            [
                'product_id' => $spotify->id,
                'name' => '1 Bulan Premium Individual',
                'price' => 19000,
                'cost_price' => 12000,
                'stock_type' => 'manual',
                'stock' => 8,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        AccountStock::firstOrCreate(
            ['credentials' => "Email: spoty.music01@gmail.com\nPassword: PlayMus1c#2026"],
            [
                'product_variant_id' => $spotVariant->id,
                'notes' => 'Akun fresh, region Indonesia, bebas dengar tanpa iklan.',
                'is_used' => false,
            ]
        );

        // 5. Seed Products - Jual Beli Akun Game
        $accMlSultan = Product::updateOrCreate(
            ['slug' => 'akun-mlbb-sultan-mythic-glory'],
            [
                'category_id' => $gameAccountCategory->id,
                'name' => 'Akun MLBB Sultan (Mythic Glory + 280 Skin)',
                'type' => ProductType::GameAccount,
                'publisher' => 'Moonton',
                'thumbnail' => 'images/products/mlbb-sultan-profile.jpg',
                'gallery' => [
                    'images/products/mlbb-sultan-profile.jpg',
                    'images/products/mlbb-sultan-skins.jpg',
                ],
                'account_specs' => [
                    'Rank Saat Ini' => 'Mythic Glory 65★',
                    'Total Skin' => '285 Skins (Sultan Collection)',
                    'Total Hero' => '122 Heroes (Full Hero)',
                    'Winrate Keseluruhan' => '68.4% (All Season)',
                    'Status Akun' => 'Monsep (Email Moonton Sepaket)',
                    'Kartu Ganti Nama (CN)' => 'Aktif / Tersedia',
                    'Emblem Set' => 'All Emblems Max Lv 60',
                    'Skin Langka Utama' => 'Granger Collector, Gusion Legend, Chou KOF, Fanny Aspirants, Lancelot Zodiac, Alucard Legend',
                ],
                'requires_user_id' => false,
                'requires_zone_id' => false,
                'description' => 'Akun Mobile Legends Sultan tier Mythic Glory 65 Bintang. Koleksi lengkap 285 Skin termasuk Collector Granger Agent Z, Legend Gusion Cosmic Gleam, KOF Chou, Aspirants Fanny, dan Zodiac Lancelot. Winrate mantap 68.4%, All Emblem Max Lv 60. Status Monsep (Email Moonton Pertama sepaket diserahkan), anti hackback dan bergaransi resmi.',
                'instructions' => 'Kredensial login akun akan dikirimkan langsung oleh Admin melalui WhatsApp setelah pembayaran dikonfirmasi.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $varSultan = ProductVariant::updateOrCreate(
            ['sku' => 'ACC-ML-SULTAN-01'],
            [
                'product_id' => $accMlSultan->id,
                'name' => 'Akun Sultan Full Skin Collector & Legend',
                'price' => 850000,
                'cost_price' => 600000,
                'stock_type' => 'manual',
                'stock' => 1,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        AccountStock::firstOrCreate(
            ['credentials' => "Login: Moonton (Email Sepaket)\nEmail: mlbb.sultan.buyer@gmail.com\nPassword: SultanMlbb#2026\nCatatan: All Unbind, Moonton sepaket email pertama, siap ganti email & password."],
            [
                'product_variant_id' => $varSultan->id,
                'notes' => 'Akun Sultan Mythic Glory 65 Bintang, Skin 280+',
                'is_used' => false,
            ]
        );

        $accMlSmurf = Product::updateOrCreate(
            ['slug' => 'akun-mlbb-smurf-ready-ranked'],
            [
                'category_id' => $gameAccountCategory->id,
                'name' => 'Akun MLBB Smurf Ready Ranked (Epic / Legend)',
                'type' => ProductType::GameAccount,
                'publisher' => 'Moonton',
                'thumbnail' => 'images/products/mlbb-smurf-profile.jpg',
                'gallery' => [
                    'images/products/mlbb-smurf-profile.jpg',
                ],
                'account_specs' => [
                    'Rank Saat Ini' => 'Epic II (5★)',
                    'Total Skin' => '35+ Skins',
                    'Total Hero' => '58 Heroes',
                    'Winrate Keseluruhan' => '74.5% High WR (412 Match)',
                    'Status Akun' => 'Monsep All Unbind Aman',
                    'Kartu Ganti Nama (CN)' => 'Tersedia',
                    'Emblem Set' => 'Assassin & Mage Max Lv 60',
                    'Hero Favorit' => 'Fanny (WR 76.2%), Gusion (WR 71.8%)',
                ],
                'requires_user_id' => false,
                'requires_zone_id' => false,
                'description' => 'Akun Smurf MLBB siap push ranked tanpa beban. WR tinggi 74.5% dari 412 match, emblem Assassin dan Mage sudah Max Lv 60. Cocok untuk Anda yang ingin push rank santai atau jasa mabar.',
                'instructions' => 'Kredensial akun dikirim langsung via WhatsApp setelah konfirmasi admin.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $varSmurf = ProductVariant::updateOrCreate(
            ['sku' => 'ACC-ML-SMURF-01'],
            [
                'product_id' => $accMlSmurf->id,
                'name' => 'Akun Smurf Rank Epic (Emblem Max)',
                'price' => 75000,
                'cost_price' => 45000,
                'stock_type' => 'manual',
                'stock' => 3,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        AccountStock::firstOrCreate(
            ['credentials' => "Login: Moonton\nEmail: smurf.mlbb01@gmail.com\nPassword: SmurfPro#2026\nCatatan: Akun fresh, siap push ranked."],
            [
                'product_variant_id' => $varSmurf->id,
                'notes' => 'Akun Smurf Epic IV',
                'is_used' => false,
            ]
        );

        $accFfOld = Product::updateOrCreate(
            ['slug' => 'akun-free-fire-old-sultan'],
            [
                'category_id' => $gameAccountCategory->id,
                'name' => 'Akun Free Fire Old (Bundle Bandit & Evo Scar)',
                'type' => ProductType::GameAccount,
                'publisher' => 'Garena',
                'thumbnail' => 'images/products/ff-old-vault.jpg',
                'gallery' => [
                    'images/products/ff-old-vault.jpg',
                ],
                'account_specs' => [
                    'Rank Saat Ini' => 'Grandmaster Season 35',
                    'Total Senjata' => '488 Senjata (95 Legendary, 58 Mythic)',
                    'Evo Guns Max' => 'Megalodon Alpha Scar Lv 7, AK Blue Flame Draco Lv 7, MP40 Cobra Lv 7',
                    'Bundle Langka' => 'Old Season 1 Sakura & Bandit Mask',
                    'K/D Ratio' => '9.45 (High KD, 78.5% Headshot)',
                    'Status Akun' => 'Login Google Aman Anti Sesi (Siap Ganti No. HP)',
                ],
                'requires_user_id' => false,
                'requires_zone_id' => false,
                'description' => 'Akun Free Fire Old Season 1 langka dengan Bundle Sakura & Bandit Mask. Dilengkapi senjata Evo Gun Megalodon Scar Max Level 7, AK Draco Lv 7, MP40 Cobra Lv 7. Rank Grandmaster dengan KD tinggi 9.45. Login Google aman tanpa kendala sesi.',
                'instructions' => 'Kredensial login akun akan dikirimkan oleh Admin via WhatsApp.',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $varFf = ProductVariant::updateOrCreate(
            ['sku' => 'ACC-FF-OLD-01'],
            [
                'product_id' => $accFfOld->id,
                'name' => 'Akun FF Old Season 1 + Evo Gun Max',
                'price' => 450000,
                'cost_price' => 300000,
                'stock_type' => 'manual',
                'stock' => 1,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        AccountStock::firstOrCreate(
            ['credentials' => "Login: Akun Google\nEmail: ff.oldsultan@gmail.com\nPassword: BooyahOld#2026\nCatatan: Jangan lupa ubah password dan nomor verifikasi setelah login."],
            [
                'product_variant_id' => $varFf->id,
                'notes' => 'Akun FF Old Season 1',
                'is_used' => false,
            ]
        );

        // 6. Seed Products - Jasa Joki Rank Game
        $jokiMlRank = Product::updateOrCreate(
            ['slug' => 'jasa-joki-rank-mobile-legends'],
            [
                'category_id' => $jokiCategory->id,
                'name' => 'Jasa Joki Rank Mobile Legends (Fast & Aman)',
                'type' => ProductType::Joki,
                'publisher' => 'Moonton',
                'requires_user_id' => true,
                'user_id_label' => 'Email / ID Akun Game',
                'requires_zone_id' => true,
                'zone_id_label' => 'Login Via (Moonton/VK/FB/TikTok/Google)',
                'description' => 'Jasa joki rank Mobile Legends profesional, proses cepat (1-12 jam tergantung paket), winrate tinggi 85%+, anti banned & anti hackback. Dikerjakan oleh booster mantan pro player.',
                'instructions' => 'Pilih Login Via, masukkan email dan password akun game Anda dengan benar. Harap matikan verifikasi 2 langkah dan jangan login saat proses joki berlangsung.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $jokiVariants = [
            ['name' => 'Joki Per Bintang Mythic (1 Bintang)', 'sku' => 'JOKI-ML-1STAR', 'price' => 15000, 'cost_price' => 10000, 'sort_order' => 1],
            ['name' => 'Paket Grandmaster ke Epic (5 Tier)', 'sku' => 'JOKI-ML-GM-EPIC', 'price' => 45000, 'cost_price' => 30000, 'sort_order' => 2],
            ['name' => 'Paket Epic ke Legend (5 Tier)', 'sku' => 'JOKI-ML-EPIC-LEGEND', 'price' => 70000, 'cost_price' => 50000, 'sort_order' => 3],
            ['name' => 'Paket Legend ke Mythic (5 Tier)', 'sku' => 'JOKI-ML-LEGEND-MYTHIC', 'price' => 110000, 'cost_price' => 80000, 'sort_order' => 4],
            ['name' => 'Paket Mythic ke Mythical Glory (50 Bintang)', 'sku' => 'JOKI-ML-MYTHIC-GLORY', 'price' => 340000, 'cost_price' => 250000, 'sort_order' => 5],
            ['name' => 'Joki Gendong / Mabar per Bintang', 'sku' => 'JOKI-ML-GENDONG', 'price' => 25000, 'cost_price' => 18000, 'sort_order' => 6],
        ];

        foreach ($jokiVariants as $jVar) {
            ProductVariant::updateOrCreate(
                ['sku' => $jVar['sku']],
                [
                    'product_id' => $jokiMlRank->id,
                    'name' => $jVar['name'],
                    'price' => $jVar['price'],
                    'cost_price' => $jVar['cost_price'],
                    'stock_type' => 'unlimited',
                    'sort_order' => $jVar['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $jokiMlWr = Product::updateOrCreate(
            ['slug' => 'joki-push-winrate-hero-mlbb'],
            [
                'category_id' => $jokiCategory->id,
                'name' => 'Joki Push Winrate Hero Favorit MLBB',
                'type' => ProductType::Joki,
                'publisher' => 'Moonton',
                'requires_user_id' => true,
                'user_id_label' => 'Email / ID Akun Game',
                'requires_zone_id' => true,
                'zone_id_label' => 'Login Via (Moonton/VK/FB)',
                'description' => 'Menaikkan winrate hero favorit Mobile Legends hingga 70%-90% di mode Classic. Bebas request hero apa saja.',
                'instructions' => 'Masukkan data akun dan tuliskan nama hero yang ingin dinaikkan winrate-nya pada catatan pesanan.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $wrVariants = [
            ['name' => 'Paket 5 Win Classic Hero', 'sku' => 'JOKI-WR-5WIN', 'price' => 35000, 'cost_price' => 25000, 'sort_order' => 1],
            ['name' => 'Paket 10 Win Classic Hero', 'sku' => 'JOKI-WR-10WIN', 'price' => 65000, 'cost_price' => 45000, 'sort_order' => 2],
        ];

        foreach ($wrVariants as $wVar) {
            ProductVariant::updateOrCreate(
                ['sku' => $wVar['sku']],
                [
                    'product_id' => $jokiMlWr->id,
                    'name' => $wVar['name'],
                    'price' => $wVar['price'],
                    'cost_price' => $wVar['cost_price'],
                    'stock_type' => 'unlimited',
                    'sort_order' => $wVar['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // 7. Seed Manual Payment Methods
        $methods = [
            [
                'code' => 'BCA',
                'name' => 'Bank BCA (Transfer Manual)',
                'group' => 'Transfer Bank',
                'account_number' => '8920192831',
                'account_holder' => 'PT Toko Digital Mandiri',
                'fee_flat' => 0,
                'fee_percent' => 0,
                'instructions' => 'Transfer tepat sesuai nominal tagihan ke rekening BCA di atas. Setelah transfer berhasil, upload screenshot/foto bukti transfer pada form di bawah.',
                'sort_order' => 1,
            ],
            [
                'code' => 'MANDIRI',
                'name' => 'Bank Mandiri (Transfer Manual)',
                'group' => 'Transfer Bank',
                'account_number' => '1370019283120',
                'account_holder' => 'PT Toko Digital Mandiri',
                'fee_flat' => 0,
                'fee_percent' => 0,
                'instructions' => 'Transfer tepat sesuai nominal tagihan ke rekening Mandiri di atas. Upload bukti transfer untuk verifikasi admin.',
                'sort_order' => 2,
            ],
            [
                'code' => 'BRI',
                'name' => 'Bank BRI (Transfer Manual)',
                'group' => 'Transfer Bank',
                'account_number' => '020601029384501',
                'account_holder' => 'PT Toko Digital Mandiri',
                'fee_flat' => 0,
                'fee_percent' => 0,
                'instructions' => 'Transfer tepat sesuai nominal tagihan ke rekening BRI di atas. Simpan bukti transfer dan unggah pada halaman invoice.',
                'sort_order' => 3,
            ],
            [
                'code' => 'DANA',
                'name' => 'DANA (Transfer E-Wallet)',
                'group' => 'E-Wallet',
                'account_number' => '085712345678',
                'account_holder' => 'Admin Digital Store',
                'fee_flat' => 0,
                'fee_percent' => 0,
                'instructions' => 'Kirim saldo DANA ke nomor di atas. Cantumkan nomor invoice pada catatan pengiriman jika memungkinkan.',
                'sort_order' => 4,
            ],
            [
                'code' => 'QRIS',
                'name' => 'QRIS Semua Pembayaran (Scan)',
                'group' => 'QRIS',
                'account_number' => 'NMID: ID102003928172',
                'account_holder' => 'Toko Digital Resmi',
                'fee_flat' => 0,
                'fee_percent' => 0,
                'instructions' => 'Scan QRIS menggunakan aplikasi M-Banking atau E-Wallet apa pun (BCA, Mandiri, BRI, GoPay, OVO, ShopeePay). Upload bukti transfer setelah berhasil.',
                'sort_order' => 5,
            ],
        ];

        foreach ($methods as $m) {
            PaymentMethod::updateOrCreate(
                ['code' => $m['code']],
                $m
            );
        }

        // 8. Seed Promo Banners (Customizable by Admin)
        $banners = [
            [
                'title' => 'PROMO SPESIAL TOP UP 24 JAM',
                'subtitle' => 'Diskon hingga 30% Diamond Mobile Legends & Free Fire, Proses Detikan!',
                'badge_text' => '⚡ EVENT PROMO',
                'target_url' => '#games',
                'gradient' => 'from-blue-900 via-indigo-950 to-slate-950',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'FLASH SALE BOOYAH PASS & DIAMONDS',
                'subtitle' => 'Top Up Diamond Game Resmi, Murah & Legal 100% Tanpa Login Akun!',
                'badge_text' => '🔥 FLASH SALE',
                'target_url' => '#games',
                'gradient' => 'from-amber-900 via-orange-950 to-slate-950',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'JUAL BELI AKUN GAME SULTAN & SMURF',
                'subtitle' => 'Akun Terverifikasi Monsep Anti Hackback Bergaransi & Kredensial Otomatis!',
                'badge_text' => '👑 AKUN SULTAN',
                'target_url' => '#game-accounts',
                'gradient' => 'from-purple-900 via-slate-950 to-emerald-950',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $b) {
            Banner::updateOrCreate(
                ['title' => $b['title']],
                $b
            );
        }
    }
}
