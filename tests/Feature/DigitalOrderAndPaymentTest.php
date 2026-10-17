<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Voucher;
use Database\Seeders\DigitalProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DigitalOrderAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DigitalProductSeeder::class);
    }

    public function test_can_create_game_topup_order_with_manual_bank_transfer(): void
    {
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Gamer Sejati',
            'customer_email' => 'gamer@gmail.com',
            'customer_phone' => '08123456789',
            'target_user_id' => '12345678',
            'target_zone_id' => '2024',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $orderNumber = $response->json('order_number');

        $this->assertDatabaseHas('orders', [
            'order_number' => $orderNumber,
            'customer_email' => 'gamer@gmail.com',
            'target_user_id' => '12345678',
            'target_zone_id' => '2024',
            'status' => OrderStatus::Pending->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'payment_gateway' => 'manual',
            'payment_method' => 'BCA',
            'account_number' => '8920192831',
            'account_holder' => 'PT Toko Digital Mandiri',
            'status' => PaymentStatus::Pending->value,
        ]);
    }

    public function test_fails_to_create_game_order_if_zone_id_missing(): void
    {
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Gamer Sejati',
            'customer_email' => 'gamer@gmail.com',
            'customer_phone' => '08123456789',
            'target_user_id' => '12345678',
            // Missing target_zone_id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['target_zone_id']);
    }

    public function test_customer_can_upload_payment_proof(): void
    {
        Storage::fake('public');

        $variant = ProductVariant::where('sku', 'ML-86')->first();

        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Gamer Sejati',
            'customer_email' => 'gamer@gmail.com',
            'customer_phone' => '08123456789',
            'target_user_id' => '12345678',
            'target_zone_id' => '2024',
        ]);

        $orderNumber = $response->json('order_number');

        // Customer uploads payment proof file
        $file = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 600);

        $uploadResponse = $this->post(route('orders.upload-proof', $orderNumber), [
            'payment_proof' => $file,
        ]);

        $uploadResponse->assertRedirect(route('orders.show', $orderNumber));
        $uploadResponse->assertSessionHas('success');

        $order = Order::where('order_number', $orderNumber)->with('payment')->first();

        $this->assertEquals(OrderStatus::Processing, $order->status);
        $this->assertNotNull($order->payment->payment_proof);
        $this->assertNotNull($order->payment->payment_proof_uploaded_at);

        Storage::disk('public')->assertExists($order->payment->payment_proof);
    }

    public function test_admin_can_view_and_confirm_manual_payment_with_auto_stock_fulfillment(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $variant = ProductVariant::where('sku', 'NF-1M-SHR')->first();
        $initialStock = $variant->stock;
        $this->assertGreaterThan(0, $initialStock);

        // 1. Customer creates order
        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Movie Lover',
            'customer_email' => 'movielover@gmail.com',
            'customer_phone' => '085712345678',
        ]);

        $orderNumber = $response->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        // 2. Customer uploads payment proof
        $file = UploadedFile::fake()->image('bukti_transfer.png');
        $this->post(route('orders.upload-proof', $orderNumber), [
            'payment_proof' => $file,
        ]);

        // 3. Admin visits order page
        $adminViewResponse = $this->actingAs($admin)->get(route('admin.orders.show', $order->id));
        $adminViewResponse->assertStatus(200);
        $adminViewResponse->assertSee('Kirim Akun via WhatsApp');

        // 4. Admin confirms payment
        $confirmResponse = $this->actingAs($admin)->post(route('admin.orders.confirm', $order->id));
        $confirmResponse->assertSessionHas('success');

        // 5. Verify order is completed
        $order->refresh();
        $this->assertEquals(OrderStatus::Completed, $order->status);
        $this->assertEquals(PaymentStatus::Paid, $order->payment->status);

        // Verify variant stock deducted
        $remainingStock = $variant->fresh()->stock;
        $this->assertEquals($initialStock - 1, $remainingStock);

        // Customer view shows WhatsApp delivery notification
        $customerViewResponse = $this->get(route('orders.show', $orderNumber));
        $customerViewResponse->assertStatus(200);
        $customerViewResponse->assertSee('Pengiriman Akun Dilakukan via WhatsApp');
    }

    public function test_admin_can_reject_manual_payment(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $variant = ProductVariant::where('sku', 'ML-86')->first();

        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Tester',
            'customer_email' => 'tester@gmail.com',
            'customer_phone' => '08123456789',
            'target_user_id' => '12345678',
            'target_zone_id' => '2024',
        ]);

        $orderNumber = $response->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        $rejectResponse = $this->actingAs($admin)->post(route('admin.orders.reject', $order->id), [
            'reason' => 'Transfer dana tidak ditemukan pada mutasi m-banking.',
        ]);

        $rejectResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(OrderStatus::Failed, $order->status);
        $this->assertEquals(PaymentStatus::Failed, $order->payment->status);
        $this->assertStringContainsString('Transfer dana tidak ditemukan', $order->payment->admin_note);
    }

    public function test_admin_can_manage_bank_accounts(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // 1. Create new bank account
        $storeResponse = $this->actingAs($admin)->post(route('admin.payment-methods.store'), [
            'code' => 'BNI',
            'name' => 'Bank Negara Indonesia (BNI)',
            'group' => 'Transfer Bank Manual',
            'account_number' => '9876543210',
            'account_holder' => 'Bendahara Toko',
            'fee_flat' => 0,
        ]);

        $storeResponse->assertRedirect(route('admin.payment-methods.index'));

        $this->assertDatabaseHas('payment_methods', [
            'code' => 'BNI',
            'account_number' => '9876543210',
            'account_holder' => 'Bendahara Toko',
        ]);

        $bni = PaymentMethod::where('code', 'BNI')->first();

        // 2. Toggle active
        $this->actingAs($admin)->patch(route('admin.payment-methods.toggle', $bni->id));
        $this->assertFalse($bni->fresh()->is_active);

        // 3. Update account
        $this->actingAs($admin)->put(route('admin.payment-methods.update', $bni->id), [
            'name' => 'Bank BNI Official',
            'group' => 'Transfer Bank Manual',
            'account_number' => '987654321099',
            'account_holder' => 'Bendahara Toko Resmi',
            'fee_flat' => 0,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('payment_methods', [
            'code' => 'BNI',
            'account_number' => '987654321099',
            'name' => 'Bank BNI Official',
        ]);

        // 4. Delete account
        $this->actingAs($admin)->delete(route('admin.payment-methods.destroy', $bni->id));
        $this->assertDatabaseMissing('payment_methods', [
            'code' => 'BNI',
        ]);
    }

    public function test_user_can_view_and_update_profile_and_change_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        // 1. Visit profile page
        $viewResponse = $this->actingAs($user)->get(route('profile.edit'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($user->name);

        // 2. Update profile data & change password
        $updateResponse = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Budi Santoso Pro',
            'email' => 'budi.pro@gmail.com',
            'phone' => '081299887766',
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $updateResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Budi Santoso Pro', $user->name);
        $this->assertEquals('budi.pro@gmail.com', $user->email);
        $this->assertEquals('081299887766', $user->phone);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_can_order_mlbb_game_account_and_auto_fulfill_credentials(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $variant = ProductVariant::where('sku', 'ACC-ML-SULTAN-01')->first();
        $initialStock = $variant->stock;

        // 1. Customer buys MLBB Sultan Account
        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Sultan Buyer',
            'customer_email' => 'sultanbuyer@gmail.com',
            'customer_phone' => '08123456789',
        ]);

        $response->assertStatus(200);
        $orderNumber = $response->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        $this->assertTrue($order->isGameAccount());

        // 2. Admin confirms payment
        $this->actingAs($admin)->post(route('admin.orders.confirm', $order->id));

        $order->refresh();
        $this->assertEquals(OrderStatus::Completed, $order->status);
        $this->assertEquals($initialStock - 1, $variant->fresh()->stock);

        // Web view confirms WhatsApp delivery to buyer
        $customerViewResponse = $this->get(route('orders.show', $orderNumber));
        $customerViewResponse->assertStatus(200);
        $customerViewResponse->assertSee('Pengiriman Akun Dilakukan via WhatsApp');
    }

    public function test_can_order_joki_rank_with_login_details(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $variant = ProductVariant::where('sku', 'JOKI-ML-EPIC-LEGEND')->first();

        // 1. Customer orders Joki
        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Joki Customer',
            'customer_email' => 'jokicustomer@gmail.com',
            'customer_phone' => '08123456789',
            'target_zone_id' => 'Moonton',
            'target_user_id' => 'player.acc@gmail.com',
            'target_nickname' => 'Rahasia123#',
            'notes' => 'Request hero Ling dan Fanny, jangan main malam',
        ]);

        $response->assertStatus(200);
        $orderNumber = $response->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        $this->assertTrue($order->isJoki());
        $this->assertEquals('Moonton', $order->target_zone_id);
        $this->assertEquals('player.acc@gmail.com', $order->target_user_id);
        $this->assertEquals('Rahasia123#', $order->target_nickname);

        // 2. Admin can view order and see joki details
        $adminView = $this->actingAs($admin)->get(route('admin.orders.show', $order->id));
        $adminView->assertStatus(200);
        $adminView->assertSee('player.acc@gmail.com');
        $adminView->assertSee('Rahasia123#');
        $adminView->assertSee('Request hero Ling');
    }

    public function test_home_displays_game_account_screenshots_and_specifications(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Jual Beli Akun Game Bergaransi');
        $response->assertSee('Akun MLBB Sultan (Mythic Glory + 280 Skin)');
        $response->assertSee('Mythic Glory 65★');
        $response->assertSee('285 Skins');
        $response->assertSee('Full Garansi');
        $response->assertSee('mlbb-sultan-profile.jpg');
    }

    public function test_checkout_displays_screenshot_gallery_and_account_specs(): void
    {
        $response = $this->get(route('products.checkout', 'akun-mlbb-sultan-mythic-glory'));

        $response->assertStatus(200);
        $response->assertSee('Bukti Screenshot & Isi Akun Game');
        $response->assertSee('Spesifikasi & Statistik Akun Lengkap');
        $response->assertSee('Mythic Glory 65★');
        $response->assertSee('285 Skins (Sultan Collection)');
        $response->assertSee('Granger Collector, Gusion Legend');
        $response->assertSee('Perbesar Layar Penuh');
        $response->assertSee('screenshot-lightbox');
    }

    public function test_admin_can_confirm_game_topup_order_and_button_disappears(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        // 1. Customer orders game topup
        $orderResponse = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Topup Gamer',
            'customer_email' => 'gamer@gmail.com',
            'customer_phone' => '08123456789',
            'target_user_id' => '12345678',
            'target_zone_id' => '2024',
        ]);
        $orderResponse->assertStatus(200);
        $orderNumber = $orderResponse->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        // 2. Customer uploads payment proof
        $file = UploadedFile::fake()->image('topup_transfer.png');
        $this->post(route('orders.upload-proof', $orderNumber), [
            'payment_proof' => $file,
        ]);

        // 3. Admin visits order page before confirmation - button should be visible
        $adminView = $this->actingAs($admin)->get(route('admin.orders.show', $order->id));
        $adminView->assertStatus(200);
        $adminView->assertSee('Konfirmasi Pembayaran Valid');

        // 4. Admin confirms payment
        $confirmResponse = $this->actingAs($admin)->post(route('admin.orders.confirm', $order->id));
        $confirmResponse->assertSessionHas('success');

        // 5. Verify order is now COMPLETED and payment is PAID
        $order->refresh();
        $this->assertEquals(OrderStatus::Completed, $order->status);
        $this->assertEquals(PaymentStatus::Paid, $order->payment->status);

        // 6. Admin visits order page again - confirm button MUST NOT appear
        $adminViewAfter = $this->actingAs($admin)->get(route('admin.orders.show', $order->id));
        $adminViewAfter->assertStatus(200);
        $adminViewAfter->assertDontSee('Konfirmasi Pembayaran Valid');
        $adminViewAfter->assertSee('Status Final');
        $adminViewAfter->assertSee('completed');
        $adminViewAfter->assertSee('Lunas');
    }

    public function test_guest_can_order_without_login(): void
    {
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        // Ensure user is guest
        $this->assertGuest();

        $response = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Tamu Tanpa Login',
            'customer_email' => 'tamu@gmail.com',
            'customer_phone' => '081234567899',
            'target_user_id' => '99887766',
            'target_zone_id' => '1234',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $orderNumber = $response->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertEquals('Tamu Tanpa Login', $order->customer_name);
    }

    public function test_guest_can_use_voucher_without_login(): void
    {
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        Voucher::create([
            'code' => 'HEMAT10',
            'name' => 'Diskon 10%',
            'type' => 'percentage',
            'discount_amount' => 10,
            'min_order_amount' => 0,
            'is_active' => true,
        ]);

        $this->assertGuest();

        // 1. Checking voucher without login succeeds
        $checkResponse = $this->postJson(route('vouchers.check'), [
            'code' => 'HEMAT10',
            'variant_id' => $variant->id,
        ]);

        $checkResponse->assertStatus(200);
        $checkResponse->assertJson([
            'valid' => true,
            'code' => 'HEMAT10',
        ]);

        // 2. Submitting order with voucher as guest succeeds
        $orderResponse = $this->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => 'Tamu Pakai Voucher',
            'customer_email' => 'tamu@gmail.com',
            'customer_phone' => '081234567899',
            'target_user_id' => '99887766',
            'target_zone_id' => '1234',
            'voucher_code' => 'HEMAT10',
        ]);

        $orderResponse->assertStatus(200);
        $orderNumber = $orderResponse->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertEquals('HEMAT10', $order->voucher_code);
        $this->assertGreaterThan(0, $order->discount_amount);
    }

    public function test_member_can_use_voucher_when_logged_in(): void
    {
        $member = User::factory()->create(['role' => UserRole::Customer]);
        $variant = ProductVariant::where('sku', 'ML-86')->first();

        Voucher::create([
            'code' => 'MEMBERHEMAT',
            'name' => 'Diskon Member Rp 5000',
            'type' => 'fixed',
            'discount_amount' => 5000,
            'min_order_amount' => 10000,
            'is_active' => true,
        ]);

        // 1. Member can check voucher
        $checkResponse = $this->actingAs($member)->postJson(route('vouchers.check'), [
            'code' => 'MEMBERHEMAT',
            'variant_id' => $variant->id,
        ]);

        $checkResponse->assertStatus(200);
        $checkResponse->assertJson(['valid' => true, 'code' => 'MEMBERHEMAT']);

        // 2. Member can place order with voucher
        $orderResponse = $this->actingAs($member)->postJson(route('orders.store'), [
            'variant_id' => $variant->id,
            'payment_method' => 'BCA',
            'customer_name' => $member->name,
            'customer_email' => $member->email,
            'customer_phone' => '081234567890',
            'target_user_id' => '12345678',
            'target_zone_id' => '2024',
            'voucher_code' => 'MEMBERHEMAT',
        ]);

        $orderResponse->assertStatus(200);
        $orderNumber = $orderResponse->json('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        $this->assertEquals($member->id, $order->user_id);
        $this->assertEquals('MEMBERHEMAT', $order->voucher_code);
        $this->assertEquals(5000, $order->discount_amount);
    }

    public function test_admin_can_easily_add_stock_numerically_without_credentials(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $variant = ProductVariant::where('sku', 'NF-1M-SHR')->first();
        $initialStock = $variant->stock;

        // Admin adds +5 stock without inputting credentials
        $response = $this->actingAs($admin)->post(route('admin.variants.add-stock', $variant->id), [
            'stock_to_add' => 5,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals($initialStock + 5, $variant->fresh()->stock);
    }

    public function test_admin_can_set_variant_stock_directly(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $variant = ProductVariant::where('sku', 'NF-1M-PRV')->first();

        // Admin sets stock directly to 25
        $response = $this->actingAs($admin)->post(route('admin.variants.add-stock', $variant->id), [
            'stock' => 25,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(25, $variant->fresh()->stock);
    }
}
