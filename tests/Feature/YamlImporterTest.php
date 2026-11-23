<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\Database\RagnarokYamlParser;
use App\Services\Database\ItemDbImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class YamlImporterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $sampleYamlContent;
    protected string $sampleYamlFile;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure in-memory ragnarok connection with standard item_db table
        config(['database.connections.ragnarok' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        Schema::connection('ragnarok')->create('item_db', function ($table) {
            $table->increments('id');
            $table->string('name_aegis', 50)->default('');
            $table->string('name_english', 100)->default('');
            $table->string('type', 20)->nullable();
            $table->string('subtype', 20)->nullable();
            $table->mediumInteger('price_buy')->nullable();
            $table->mediumInteger('price_sell')->nullable();
            $table->smallInteger('weight')->nullable();
            $table->smallInteger('attack')->nullable();
            $table->smallInteger('defense')->nullable();
            $table->tinyInteger('range')->nullable();
            $table->tinyInteger('slots')->nullable();

            $table->tinyInteger('job_all')->nullable();
            $table->tinyInteger('job_novice')->nullable();
            $table->tinyInteger('job_swordman')->nullable();
            $table->tinyInteger('job_mage')->nullable();
            $table->tinyInteger('job_archer')->nullable();
            $table->tinyInteger('job_acolyte')->nullable();
            $table->tinyInteger('job_merchant')->nullable();
            $table->tinyInteger('job_thief')->nullable();
            $table->tinyInteger('job_knight')->nullable();
            $table->tinyInteger('job_priest')->nullable();
            $table->tinyInteger('job_wizard')->nullable();
            $table->tinyInteger('job_blacksmith')->nullable();
            $table->tinyInteger('job_hunter')->nullable();
            $table->tinyInteger('job_assassin')->nullable();
            $table->tinyInteger('job_crusader')->nullable();
            $table->tinyInteger('job_monk')->nullable();
            $table->tinyInteger('job_sage')->nullable();
            $table->tinyInteger('job_rogue')->nullable();
            $table->tinyInteger('job_alchemist')->nullable();
            $table->tinyInteger('job_barddancer')->nullable();
            $table->tinyInteger('job_supernovice')->nullable();
            $table->tinyInteger('job_gunslinger')->nullable();
            $table->tinyInteger('job_ninja')->nullable();
            $table->tinyInteger('job_taekwon')->nullable();
            $table->tinyInteger('job_stargladiator')->nullable();
            $table->tinyInteger('job_soullinker')->nullable();

            $table->tinyInteger('class_all')->nullable();
            $table->tinyInteger('class_normal')->nullable();
            $table->tinyInteger('class_upper')->nullable();
            $table->tinyInteger('class_baby')->nullable();

            $table->string('gender', 10)->nullable();
            $table->tinyInteger('location_head_top')->nullable();
            $table->tinyInteger('location_head_mid')->nullable();
            $table->tinyInteger('location_head_low')->nullable();
            $table->tinyInteger('location_armor')->nullable();
            $table->tinyInteger('location_right_hand')->nullable();
            $table->tinyInteger('location_left_hand')->nullable();
            $table->tinyInteger('location_garment')->nullable();
            $table->tinyInteger('location_shoes')->nullable();
            $table->tinyInteger('location_right_accessory')->nullable();
            $table->tinyInteger('location_left_accessory')->nullable();

            $table->tinyInteger('weapon_level')->nullable();
            $table->tinyInteger('armor_level')->nullable();
            $table->smallInteger('equip_level_min')->nullable();
            $table->smallInteger('equip_level_max')->nullable();
            $table->tinyInteger('refineable')->nullable();
            $table->smallInteger('view')->nullable();
            $table->string('alias_name', 50)->nullable();

            $table->tinyInteger('flag_buyingstore')->nullable();
            $table->tinyInteger('flag_deadbranch')->nullable();
            $table->tinyInteger('flag_container')->nullable();
            $table->tinyInteger('flag_uniqueid')->nullable();
            $table->tinyInteger('flag_bindonequip')->nullable();
            $table->tinyInteger('flag_dropannounce')->nullable();
            $table->tinyInteger('flag_noconsume')->nullable();
            $table->string('flag_dropeffect', 20)->nullable();

            $table->text('script')->nullable();
            $table->text('equip_script')->nullable();
            $table->text('unequip_script')->nullable();
        });

        $this->admin = User::create([
            'username' => 'sysadmin',
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->sampleYamlContent = <<<YAML
# Official rAthena Item Database Sample
Header:
  Type: ITEM_DB
  Version: 3

Body:
  - Id: 1101
    AegisName: Sword
    Name: Sword
    Type: Weapon
    SubType: 1hSword
    Buy: 100
    Weight: 500
    Attack: 25
    Range: 1
    Slots: 3
    Jobs:
      Swordman: true
      Knight: true
      Crusader: true
    Locations:
      Right_Hand: true
    WeaponLevel: 1
    EquipLevelMin: 2
    Refineable: true
    Script: >
      bonus bAtk,5;
      bonus bHit,2;

  - Id: 1201
    AegisName: Knife
    Name: Knife
    Type: Weapon
    SubType: Dagger
    Buy: 50
    Weight: 400
    Attack: 17
    Range: 1
    Slots: 3
    Jobs:
      All: true
    Classes:
      All: true
    Locations:
      Right_Hand: true
    WeaponLevel: 1
    Refineable: true

Footer:
  Imports:
    - Path: db/pre-re/item_db_usable.yml
YAML;

        $this->sampleYamlFile = tempnam(sys_get_temp_dir(), 'test_yaml_') . '.yml';
        file_put_contents($this->sampleYamlFile, $this->sampleYamlContent);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->sampleYamlFile)) {
            @unlink($this->sampleYamlFile);
        }
        parent::tearDown();
    }

    public function test_parser_inspects_header_and_streams_items(): void
    {
        $parser = new RagnarokYamlParser();
        $header = $parser->inspectHeader($this->sampleYamlFile);

        $this->assertEquals('ITEM_DB', $header['type']);
        $this->assertEquals(3, $header['version']);

        $items = iterator_to_array($parser->streamItems($this->sampleYamlFile));
        $this->assertCount(2, $items);

        $sword = $items[0];
        $this->assertEquals(1101, $sword['Id']);
        $this->assertEquals('Sword', $sword['AegisName']);
        $this->assertEquals('1hSword', $sword['SubType']);
        $this->assertEquals(25, $sword['Attack']);
        $this->assertTrue($sword['Refineable']);
        $this->assertTrue($sword['Jobs']['Swordman']);
        $this->assertStringContainsString('bonus bAtk,5;', $sword['Script']);

        $knife = $items[1];
        $this->assertEquals(1201, $knife['Id']);
        $this->assertTrue($knife['Jobs']['All']);
    }

    public function test_importer_service_ingests_items_into_database(): void
    {
        $importer = new ItemDbImporter(new RagnarokYamlParser());
        $result = $importer->import($this->sampleYamlFile, 'item_db', 'upsert');

        $this->assertEquals('success', $result['status']);
        $this->assertEquals(2, $result['processed']);
        $this->assertEquals(2, $result['imported']);

        $this->assertDatabaseHas('item_db', [
            'id' => 1101,
            'name_aegis' => 'Sword',
            'name_english' => 'Sword',
            'type' => 'Weapon',
            'subtype' => '1hSword',
            'price_buy' => 100,
            'price_sell' => 50,
            'attack' => 25,
            'job_swordman' => 1,
            'location_right_hand' => 1,
            'refineable' => 1,
        ], 'ragnarok');

        $this->assertDatabaseHas('item_db', [
            'id' => 1201,
            'name_aegis' => 'Knife',
            'job_all' => 1,
            'class_all' => 1,
        ], 'ragnarok');
    }

    public function test_importer_service_truncate_mode(): void
    {
        $importer = new ItemDbImporter(new RagnarokYamlParser());

        // First import
        $importer->import($this->sampleYamlFile, 'item_db', 'upsert');
        $this->assertEquals(2, DB::connection('ragnarok')->table('item_db')->count());

        // Second import with truncate
        $importer->import($this->sampleYamlFile, 'item_db', 'truncate');
        $this->assertEquals(2, DB::connection('ragnarok')->table('item_db')->count());
    }

    public function test_artisan_command_imports_yaml(): void
    {
        $this->artisan('aether:import-yaml', [
            'file_or_url' => $this->sampleYamlFile,
            '--table' => 'item_db',
            '--mode' => 'upsert',
        ])
        ->expectsOutputToContain('Import Completed Successfully!')
        ->assertExitCode(0);

        $this->assertEquals(2, DB::connection('ragnarok')->table('item_db')->count());
    }

    public function test_admin_can_view_importer_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/importer');
        $response->assertStatus(200);
        $response->assertSeeText('Database & YAML Importer');
        $response->assertSeeText('Official Item DB (item_db)');
        $response->assertSeeText('Execute Streaming YAML Ingestion');
    }

    public function test_admin_can_upload_and_import_yaml(): void
    {
        $uploadedFile = UploadedFile::fake()->createWithContent('custom_items.yml', $this->sampleYamlContent);

        $response = $this->actingAs($this->admin)->post('/admin/importer', [
            'source_type' => 'upload',
            'target_table' => 'item_db',
            'mode' => 'upsert',
            'yaml_file' => $uploadedFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('import_result');

        $this->assertEquals(2, DB::connection('ragnarok')->table('item_db')->count());
    }

    public function test_admin_can_import_from_server_file_path(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/importer', [
            'source_type' => 'path',
            'target_table' => 'item_db',
            'mode' => 'upsert',
            'file_path' => $this->sampleYamlFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(2, DB::connection('ragnarok')->table('item_db')->count());
    }
}
