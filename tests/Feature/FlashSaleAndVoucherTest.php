<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Voucher;
use Database\Seeders\DigitalProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleAndVoucherTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DigitalProductSeeder::class);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'email' => 'admin_test@solustion411.id',
        ]);

        $this->customer = User::factory()->create([
            'role' => UserRole::Customer,
            'email' => 'customer_test@gmail.com',
        ]);
    }

    public function test_admin_can_view_and_create_flash_sale_event(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.flash-sales.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Flash Sale Event Hari Ini');

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.flash-sales.store'), [
            'title' => 'SUPER SALE DIAMOND MLBB',
            'badge_text' => 'HEMAT 50%',
            'discount_percentage' => 50,
            'subtitle' => 'Diskon diamond termurah hanya hari ini.',
            'target_url' => '#group-game',
            'end_time' => now()->addHours(6)->toDateTimeString(),
            'is_active' => '1',
        ]);

        $storeResponse->assertRedirect(route('admin.flash-sales.index'));

        $this->assertDatabaseHas('flash_sales', [
            'title' => 'SUPER SALE DIAMOND MLBB',
            'badge_text' => 'HEMAT 50%',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_toggle_flash_sale_status(): void
    {
        $fs = FlashSale::create([
            'title' => 'FLASH SALE MALAM',
            'badge_text' => 'HEMAT 40%',
            'is_active' => true,
            'end_time' => now()->addHours(5),
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.flash-sales.toggle', $fs));
        $response->assertSessionHas('success');

        $this->assertFalse($fs->fresh()->is_active);
    }

    public function test_homepage_shows_active_flash_sale(): void
    {
        FlashSale::create([
            'title' => 'FLASH SALE MLBB TERBARU',
            'badge_text' => 'DISKON 70%',
            'subtitle' => 'Promo diamond paling hemat tahun ini.',
            'is_active' => true,
            'end_time' => now()->addHours(8),
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('FLASH SALE MLBB TERBARU');
        $response->assertSee('DISKON 70%');
        $response->assertSee('Promo diamond paling hemat tahun ini.');
    }

    public function test_admin_can_view_and_create_voucher(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.vouchers.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Voucher & Kode Promo');

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.vouchers.store'), [
            'code' => 'TESTVOUCHER20',
            'name' => 'Voucher Diskon Test 20%',
            'description' => 'Diskon khusus testing voucher',
            'type' => 'percentage',
            'discount_amount' => 20,
            'min_order_amount' => 10000,
            'max_discount' => 30000,
            'usage_limit' => 50,
            'starts_at' => now()->toDateTimeString(),
            'expires_at' => now()->addDays(7)->toDateTimeString(),
            'is_active' => '1',
        ]);

        $storeResponse->assertRedirect(route('admin.vouchers.index'));

        $this->assertDatabaseHas('vouchers', [
            'code' => 'TESTVOUCHER20',
            'type' => 'percentage',
            'discount_amount' => 20,
            'is_active' => true,
        ]);
    }

    public function test_customer_can_validate_voucher_via_api(): void
    {
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        $voucher = Voucher::create([
            'code' => 'HEMATMLBB',
            'name' => 'Hemat MLBB Rp 5.000',
            'type' => 'fixed',
            'discount_amount' => 5000,
            'min_order_amount' => 15000,
            'usage_limit' => 10,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('vouchers.check'), [
            'code' => 'HEMATMLBB',
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'code' => 'HEMATMLBB',
            'discount_amount' => 5000,
        ]);
    }

    public function test_customer_cannot_use_expired_or_insufficient_min_order_voucher(): void
    {
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        // Voucher with high min spend
        Voucher::create([
            'code' => 'SULTANMIN100K',
            'name' => 'Voucher Sultan',
            'type' => 'fixed',
            'discount_amount' => 10000,
            'min_order_amount' => 500000,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('vouchers.check'), [
            'code' => 'SULTANMIN100K',
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'valid' => false,
        ]);
    }

    public function test_checkout_applies_voucher_discount_to_order_and_payment(): void
    {
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        $voucher = Voucher::create([
            'code' => 'POTONGAN5000',
            'name' => 'Diskon Lima Ribu',
            'type' => 'fixed',
            'discount_amount' => 5000,
            'min_order_amount' => 10000,
            'usage_limit' => 10,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Pembeli Cerdas',
            'customer_email' => 'cerdas@gmail.com',
            'customer_phone' => '081234567890',
            'target_user_id' => '12345678',
            'target_zone_id' => '2024',
            'voucher_code' => 'POTONGAN5000',
        ]);

        $response->assertStatus(200);
        $orderNumber = $response->json('order_number');

        $order = Order::with('payment')->where('order_number', $orderNumber)->firstOrFail();

        $this->assertEquals('POTONGAN5000', $order->voucher_code);
        $this->assertEquals(5000, (float) $order->discount_amount);

        // Expected subtotal - 5000 + admin fee
        $expectedPayable = max(0, (float) $variant->price - 5000);
        $this->assertEquals($expectedPayable + (float) $order->admin_fee, (float) $order->total_amount);
        $this->assertEquals($expectedPayable, (float) $order->payment->amount);

        // Voucher usage incremented
        $this->assertEquals(1, $voucher->fresh()->used_count);

        // Invoice display
        $invoiceResponse = $this->get(route('orders.show', $orderNumber));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee('Diskon Voucher');
        $invoiceResponse->assertSee('POTONGAN5000');
    }
}
