<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Donation;
use App\Models\DonationPackage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected DonationPackage $package;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure in-memory rAthena game tables for in-game reward testing
        config(['database.connections.ragnarok' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        Schema::connection('ragnarok')->create('acc_reg_num', function ($table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('key', 32);
            $table->integer('index')->default(0);
            $table->bigInteger('value')->default(0);
        });

        Schema::connection('ragnarok')->create('storage', function ($table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('nameid');
            $table->integer('amount');
            $table->integer('equip')->default(0);
            $table->tinyInteger('identify')->default(1);
            $table->tinyInteger('refine')->default(0);
            $table->tinyInteger('attribute')->default(0);
            $table->integer('card0')->default(0);
            $table->integer('card1')->default(0);
            $table->integer('card2')->default(0);
            $table->integer('card3')->default(0);
            $table->integer('expire_time')->default(0);
            $table->tinyInteger('bound')->default(0);
            $table->bigInteger('unique_id')->default(0);
        });

        $this->user = User::create([
            'account_id' => 2000001,
            'username' => 'adventurer',
            'name' => 'Midgard Adventurer',
            'email' => 'adventurer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'account_id' => 2000002,
            'username' => 'adminmaster',
            'name' => 'Head GM',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->package = DonationPackage::create([
            'name' => 'Valkyrie Grace Tier',
            'description' => 'Generous adventurer package with bonus cash points and Yggdrasil Berries.',
            'price' => 25.00,
            'currency' => 'USD',
            'points' => 2500,
            'bonus_points' => 500,
            'item_id' => 607, // Yggdrasil Berry
            'item_amount' => 15,
            'item_name' => 'Yggdrasil Berry',
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SiteSetting::set('paypal_enabled', 1);
        SiteSetting::set('paypal_email', 'billing@freynix-ro.com');
        SiteSetting::set('paypal_sandbox', 1);
        SiteSetting::set('paypal_currency', 'USD');
    }

    public function test_donation_page_renders_packages_and_rewards(): void
    {
        $response = $this->get('/donations');
        $response->assertStatus(200);
        $response->assertSee('Valkyrie Grace Tier');
        $response->assertSee('$25.00');
        $response->assertSee('3,000 Points'); // 2500 + 500
        $response->assertSee('15x Yggdrasil Berry');
    }

    public function test_authenticated_user_can_submit_manual_donation(): void
    {
        $response = $this->actingAs($this->user)->post('/donations/submit', [
            'package_id' => $this->package->id,
            'transaction_reference' => 'TXN-MANUAL-7788',
            'notes' => 'Transferred via offline bank deposit.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('donations', [
            'account_id' => 2000001,
            'package_id' => $this->package->id,
            'amount' => 25.00,
            'points_awarded' => 3000,
            'payment_method' => 'manual',
            'transaction_reference' => 'TXN-MANUAL-7788',
            'status' => 'pending',
            'points_delivered' => false,
            'items_delivered' => false,
        ]);
    }

    public function test_paypal_checkout_creates_pending_donation_and_returns_redirect(): void
    {
        $response = $this->actingAs($this->user)->post(route('donations.paypal.checkout', $this->package->id));

        $response->assertStatus(200);
        $response->assertSee('Connecting to PayPal');
        $response->assertSee('billing@freynix-ro.com');
        $response->assertSee('Valkyrie Grace Tier');
        $response->assertSee('25.00');

        $this->assertDatabaseHas('donations', [
            'account_id' => 2000001,
            'package_id' => $this->package->id,
            'amount' => 25.00,
            'payment_method' => 'paypal',
            'status' => 'pending',
        ]);
    }

    public function test_paypal_ipn_webhook_verifies_and_delivers_cash_points_and_items(): void
    {
        // Setup pending PayPal donation
        $donation = Donation::create([
            'account_id' => 2000001,
            'package_id' => $this->package->id,
            'amount' => 25.00,
            'currency' => 'USD',
            'points_awarded' => 3000,
            'payment_method' => 'paypal',
            'transaction_reference' => 'PP-TXN-12345678',
            'status' => 'pending',
        ]);

        // Mock PayPal handshake confirmation
        Http::fake([
            'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr*' => Http::response('VERIFIED', 200),
        ]);

        $ipnPayload = [
            'custom' => (string) $donation->id,
            'txn_id' => 'PAYPAL-TXN-VERIFIED-99',
            'payment_status' => 'Completed',
            'mc_gross' => '25.00',
            'mc_currency' => 'USD',
            'receiver_email' => 'billing@freynix-ro.com',
            'payer_email' => 'buyer@example.com',
            'payer_id' => 'PAYER123',
        ];

        $response = $this->post('/donations/paypal/ipn', $ipnPayload);
        $response->assertStatus(200);
        $response->assertSee('OK');

        // Verify donation record was approved
        $donation->refresh();
        $this->assertEquals('approved', $donation->status);
        $this->assertTrue((bool) $donation->points_delivered);
        $this->assertTrue((bool) $donation->items_delivered);
        $this->assertEquals('buyer@example.com', $donation->payer_email);
        $this->assertEquals('PAYER123', $donation->payer_id);

        // Verify #CASHPOINTS was credited in acc_reg_num
        $reg = DB::connection('ragnarok')
            ->table('acc_reg_num')
            ->where('account_id', 2000001)
            ->where('key', '#CASHPOINTS')
            ->first();

        $this->assertNotNull($reg);
        $this->assertEquals(3000, $reg->value);

        // Verify item reward was placed directly in game storage
        $storageItem = DB::connection('ragnarok')
            ->table('storage')
            ->where('account_id', 2000001)
            ->where('nameid', 607)
            ->first();

        $this->assertNotNull($storageItem);
        $this->assertEquals(15, $storageItem->amount);
        $this->assertEquals(1, $storageItem->identify);
    }

    public function test_paypal_ipn_is_idempotent(): void
    {
        $donation = Donation::create([
            'account_id' => 2000001,
            'package_id' => $this->package->id,
            'amount' => 25.00,
            'currency' => 'USD',
            'points_awarded' => 3000,
            'payment_method' => 'paypal',
            'transaction_reference' => 'PP-TXN-IDEMPOTENT',
            'status' => 'pending',
        ]);

        Http::fake([
            'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr*' => Http::response('VERIFIED', 200),
        ]);

        $ipnPayload = [
            'custom' => (string) $donation->id,
            'txn_id' => 'PAYPAL-TXN-IDEM-01',
            'payment_status' => 'Completed',
            'mc_gross' => '25.00',
            'mc_currency' => 'USD',
            'receiver_email' => 'billing@freynix-ro.com',
            'payer_email' => 'buyer@example.com',
        ];

        // First IPN call
        $this->post('/donations/paypal/ipn', $ipnPayload)->assertStatus(200);

        // Second IPN call (e.g. duplicate webhook from PayPal)
        $this->post('/donations/paypal/ipn', $ipnPayload)->assertStatus(200);

        // Assert points were NOT doubled
        $points = DB::connection('ragnarok')
            ->table('acc_reg_num')
            ->where('account_id', 2000001)
            ->where('key', '#CASHPOINTS')
            ->value('value');
        $this->assertEquals(3000, $points);

        // Assert items were NOT duplicated
        $itemRows = DB::connection('ragnarok')
            ->table('storage')
            ->where('account_id', 2000001)
            ->where('nameid', 607)
            ->count();
        $this->assertEquals(1, $itemRows);
    }

    public function test_admin_manual_approval_delivers_points_and_items(): void
    {
        $donation = Donation::create([
            'account_id' => 2000001,
            'package_id' => $this->package->id,
            'amount' => 25.00,
            'currency' => 'USD',
            'points_awarded' => 3000,
            'payment_method' => 'manual',
            'transaction_reference' => 'MANUAL-PROOF-9900',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.donations.status', $donation->id), [
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $donation->refresh();

        $this->assertEquals('approved', $donation->status);
        $this->assertTrue((bool) $donation->points_delivered);
        $this->assertTrue((bool) $donation->items_delivered);

        $points = DB::connection('ragnarok')
            ->table('acc_reg_num')
            ->where('account_id', 2000001)
            ->where('key', '#CASHPOINTS')
            ->value('value');
        $this->assertEquals(3000, $points);

        $items = DB::connection('ragnarok')
            ->table('storage')
            ->where('account_id', 2000001)
            ->where('nameid', 607)
            ->first();
        $this->assertNotNull($items);
        $this->assertEquals(15, $items->amount);
    }
}
