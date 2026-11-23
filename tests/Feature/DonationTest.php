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

        Schema::connection('ragnarok')->create('item_db', function ($table) {
            $table->increments('id');
            $table->string('name_aegis', 50)->default('');
            $table->string('name_english', 100)->default('');
            $table->string('type', 20)->default('Etc');
            $table->tinyInteger('slots')->default(0);
        });

        Schema::connection('ragnarok')->create('login', function ($table) {
            $table->increments('account_id');
            $table->string('userid', 23)->unique();
            $table->string('user_pass', 32)->default('');
            $table->string('sex', 1)->default('M');
            $table->string('email', 39)->default('');
            $table->integer('group_id')->default(0);
            $table->integer('state')->default(0);
        });

        DB::connection('ragnarok')->table('login')->insert([
            'account_id' => 2000001,
            'userid' => 'adventurer',
            'user_pass' => md5('password'),
            'sex' => 'M',
            'email' => 'adventurer@example.com',
            'group_id' => 0,
            'state' => 0,
        ]);

        DB::connection('ragnarok')->table('item_db')->insert([
            ['id' => 607, 'name_aegis' => 'Yggdrasilberry', 'name_english' => 'Yggdrasil Berry', 'type' => 'Healing', 'slots' => 0],
            ['id' => 2220, 'name_aegis' => 'Helm_Of_Sun_God', 'name_english' => 'Helm of Sun God', 'type' => 'Armor', 'slots' => 1],
        ]);

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

    public function test_admin_can_view_donation_packages_and_rewards_customizer(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/donations/packages');
        $response->assertStatus(200);
        $response->assertSeeText('Donation Packages & Reward Customizer');
        $response->assertSeeText('Valkyrie Grace Tier');
        $response->assertSeeText('Bonus In-Game Item Reward');
    }

    public function test_admin_can_create_donation_package_with_item_rewards(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/donations/packages', [
            'name' => 'Solar God Pack',
            'description' => 'Crown yourself with the Helm of the Sun.',
            'price' => 50.00,
            'currency' => 'USD',
            'points' => 5000,
            'bonus_points' => 1000,
            'item_id' => 2220,
            'item_amount' => 1,
            'item_name' => 'Helm of Sun God',
            'is_popular' => 1,
            'is_active' => 1,
            'sort_order' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('donation_packages', [
            'name' => 'Solar God Pack',
            'price' => 50.00,
            'points' => 5000,
            'bonus_points' => 1000,
            'item_id' => 2220,
            'item_amount' => 1,
            'item_name' => 'Helm of Sun God',
            'is_popular' => true,
        ]);
    }

    public function test_admin_can_update_existing_donation_package(): void
    {
        $response = $this->actingAs($this->admin)->put("/admin/donations/packages/{$this->package->id}", [
            'name' => 'Valkyrie Grace Deluxe',
            'description' => 'Updated tier description with extra berries.',
            'price' => 30.00,
            'currency' => 'USD',
            'points' => 3000,
            'bonus_points' => 700,
            'item_id' => 607,
            'item_amount' => 25,
            'item_name' => 'Yggdrasil Berry',
            'is_popular' => 1,
            'is_active' => 1,
            'sort_order' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->package->refresh();
        $this->assertEquals('Valkyrie Grace Deluxe', $this->package->name);
        $this->assertEquals(30.00, (float) $this->package->price);
        $this->assertEquals(25, $this->package->item_amount);
    }

    public function test_admin_can_delete_and_toggle_package(): void
    {
        // Toggle
        $this->actingAs($this->admin)->post("/admin/donations/packages/{$this->package->id}/toggle")->assertRedirect();
        $this->package->refresh();
        $this->assertFalse((bool) $this->package->is_active);

        // Delete
        $this->actingAs($this->admin)->delete("/admin/donations/packages/{$this->package->id}")->assertRedirect();
        $this->assertDatabaseMissing('donation_packages', ['id' => $this->package->id]);
    }

    public function test_admin_can_save_donation_guidelines_and_instructions(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/donations/settings', [
            'donation_instructions' => 'Special promo: 2x Cash Points this weekend only!',
            'donation_payment_details' => 'GCash: 0917-000-0000 | Bank: BDO 1234567890',
            'donation_kafra_guide' => 'Speak with the Prontera Central Kafra employee to retrieve your storage rewards.',
            'donation_terms' => 'All donations are final and voluntary gifts.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Special promo: 2x Cash Points this weekend only!', SiteSetting::get('donation_instructions'));
        $this->assertEquals('GCash: 0917-000-0000 | Bank: BDO 1234567890', SiteSetting::get('donation_payment_details'));
        $this->assertEquals('Speak with the Prontera Central Kafra employee to retrieve your storage rewards.', SiteSetting::get('donation_kafra_guide'));
        $this->assertEquals('All donations are final and voluntary gifts.', SiteSetting::get('donation_terms'));

        // Public page reflects the custom guidelines and instructions
        $pub = $this->get('/donations');
        $pub->assertStatus(200);
        $pub->assertSeeText('Special promo: 2x Cash Points this weekend only!');
        $pub->assertSeeText('Speak with the Prontera Central Kafra employee to retrieve your storage rewards.');
    }

    public function test_admin_cannot_assign_item_not_in_database(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/donations/packages', [
            'name' => 'Invalid Item Package',
            'price' => 15.00,
            'currency' => 'USD',
            'points' => 1000,
            'item_id' => 999999, // Does not exist
            'item_amount' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('donation_packages', ['name' => 'Invalid Item Package']);
    }

    public function test_admin_can_search_items_from_database(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/items/search?q=Yggdrasil');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => 607,
            'name' => 'Yggdrasil Berry',
            'type' => 'Healing',
        ]);
    }

    public function test_admin_can_reset_donation_guides_to_defaults(): void
    {
        SiteSetting::set('donation_instructions', 'Old text');
        $response = $this->actingAs($this->admin)->post('/admin/donations/settings/reset');
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertStringContainsString('FreynixRO', SiteSetting::get('donation_instructions'));
        $this->assertStringContainsString('Kafra', SiteSetting::get('donation_kafra_guide'));
    }

    public function test_storage_delivery_increments_existing_item_stack(): void
    {
        // Player already has 10x Yggdrasil Berries in storage
        DB::connection('ragnarok')->table('storage')->insert([
            'account_id' => 2000001,
            'nameid' => 607,
            'amount' => 10,
            'equip' => 0,
            'identify' => 1,
            'refine' => 0,
            'attribute' => 0,
            'card0' => 0,
            'card1' => 0,
            'card2' => 0,
            'card3' => 0,
            'expire_time' => 0,
            'bound' => 0,
            'unique_id' => 0,
        ]);

        $donation = Donation::create([
            'account_id' => 2000001,
            'package_id' => $this->package->id, // Package has 15x Yggdrasil Berry
            'amount' => 25.00,
            'currency' => 'USD',
            'points_awarded' => 3000,
            'payment_method' => 'manual',
            'transaction_reference' => 'STACK-TEST-001',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)->post(route('admin.donations.status', $donation->id), [
            'status' => 'approved',
        ])->assertRedirect();

        // Verify storage still has only 1 row for item 607, with incremented amount = 25 (10 + 15)
        $storageRows = DB::connection('ragnarok')
            ->table('storage')
            ->where('account_id', 2000001)
            ->where('nameid', 607)
            ->get();

        $this->assertCount(1, $storageRows);
        $this->assertEquals(25, $storageRows->first()->amount);
    }

    public function test_admin_can_grant_direct_reward_and_points_to_player(): void
    {
        // 1. Grant 5000 Cash Points and 20x Yggdrasil Berry to account 2000001
        $response = $this->actingAs($this->admin)->post(route('admin.donations.grant'), [
            'identifier' => '2000001',
            'points' => 5000,
            'item_id' => 607,
            'item_amount' => 20,
            'notes' => 'GM Event First Place Prize',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check points in acc_reg_num
        $pointsReg = DB::connection('ragnarok')
            ->table('acc_reg_num')
            ->where('account_id', 2000001)
            ->where('key', '#CASHPOINTS')
            ->first();

        $this->assertNotNull($pointsReg);
        $this->assertEquals(5000, $pointsReg->value);

        // Check item delivered to storage
        $storageItem = DB::connection('ragnarok')
            ->table('storage')
            ->where('account_id', 2000001)
            ->where('nameid', 607)
            ->first();

        $this->assertNotNull($storageItem);
        $this->assertEquals(20, $storageItem->amount);

        // Verify donation ledger record created
        $this->assertDatabaseHas('donations', [
            'account_id' => 2000001,
            'points_awarded' => 5000,
            'payment_method' => 'admin_grant',
            'status' => 'approved',
            'points_delivered' => 1,
            'items_delivered' => 1,
        ]);

        // 2. Invalid account returns error
        $badAccRes = $this->actingAs($this->admin)->post(route('admin.donations.grant'), [
            'identifier' => '9999999',
            'points' => 100,
        ]);
        $badAccRes->assertSessionHas('error');

        // 3. Non-existent item returns error
        $badItemRes = $this->actingAs($this->admin)->post(route('admin.donations.grant'), [
            'identifier' => '2000001',
            'item_id' => 999999,
            'item_amount' => 1,
        ]);
        $badItemRes->assertSessionHas('error');
    }
}
