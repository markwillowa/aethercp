<?php

namespace App\Services\Database;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ItemDbImporter
{
    public function __construct(
        protected RagnarokYamlParser $parser
    ) {}

    /**
     * Import a rAthena / Hercules YAML item database into the emulator database.
     *
     * @param string $filePath Absolute path to the YAML file
     * @param string $targetTable Target table name ('item_db' or 'item_db2')
     * @param string $mode 'upsert' | 'truncate' | 'insert_missing'
     * @param callable|null $progressCallback Optional callback ($processed, $imported)
     * @return array{
     *   status: string,
     *   table: string,
     *   mode: string,
     *   processed: int,
     *   imported: int,
     *   elapsed_seconds: float,
     *   header: array
     * }
     */
    public function import(
        string $filePath,
        string $targetTable = 'item_db',
        string $mode = 'upsert',
        ?callable $progressCallback = null
    ): array {
        $startTime = microtime(true);

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \InvalidArgumentException("YAML database file does not exist or is unreadable: {$filePath}");
        }

        $validTables = ['item_db', 'item_db2', 'item_db_re', 'item_db2_re'];
        if (!in_array($targetTable, $validTables, true)) {
            $targetTable = 'item_db';
        }

        $conn = DB::connection('ragnarok');

        // Check if table exists in game database
        if (!Schema::connection('ragnarok')->hasTable($targetTable)) {
            throw new \RuntimeException("Target table '{$targetTable}' does not exist on game connection 'ragnarok'.");
        }

        // Cache existing table columns so we only insert valid fields
        $existingCols = Schema::connection('ragnarok')->getColumnListing($targetTable);
        $colLookup = array_flip($existingCols);

        // Inspect header
        $header = $this->parser->inspectHeader($filePath);

        // If truncate mode requested, wipe table before streaming
        if ($mode === 'truncate') {
            $conn->table($targetTable)->truncate();
        }

        $batch = [];
        $batchSize = 150;
        $processed = 0;
        $imported = 0;

        foreach ($this->parser->streamItems($filePath) as $rawItem) {
            $processed++;
            $mapped = $this->mapItemToSchema($rawItem, $colLookup);
            if (!empty($mapped['id'])) {
                $batch[] = $mapped;
            }

            if (count($batch) >= $batchSize) {
                $imported += $this->flushBatch($conn, $targetTable, $batch, $mode);
                $batch = [];

                if ($progressCallback) {
                    $progressCallback($processed, $imported);
                }
            }
        }

        // Flush remaining items
        if (!empty($batch)) {
            $imported += $this->flushBatch($conn, $targetTable, $batch, $mode);
            if ($progressCallback) {
                $progressCallback($processed, $imported);
            }
        }

        $elapsed = round(microtime(true) - $startTime, 2);

        // Invalidate AetherCP item caches
        Cache::forget('aether_item_db_name_cols_' . $targetTable);

        // Log administrative activity
        try {
            $user = auth()->user();
            ActivityLog::log(
                'admin.itemdb_import',
                "Imported {$imported} items into {$targetTable} from YAML (Mode: {$mode}, Took: {$elapsed}s)",
                $user?->account_id
            );
        } catch (\Throwable $e) {
            // Ignore logging error if running in isolated CLI
        }

        return [
            'status' => 'success',
            'table' => $targetTable,
            'mode' => $mode,
            'processed' => $processed,
            'imported' => $imported,
            'elapsed_seconds' => $elapsed,
            'header' => $header,
        ];
    }

    /**
     * Flush a batch of normalized items to the database according to mode.
     */
    protected function flushBatch($conn, string $table, array $batch, string $mode): int
    {
        if (empty($batch)) {
            return 0;
        }

        try {
            if ($mode === 'insert_missing') {
                $conn->table($table)->insertOrIgnore($batch);
                return count($batch);
            }

            if ($mode === 'truncate') {
                $conn->table($table)->insert($batch);
                return count($batch);
            }

            // Default 'upsert': update columns except 'id'
            $sample = $batch[0];
            $updateColumns = array_values(array_diff(array_keys($sample), ['id']));
            $conn->table($table)->upsert($batch, ['id'], $updateColumns);

            return count($batch);
        } catch (\Throwable $e) {
            Log::error("Failed flushing item_db batch: " . $e->getMessage(), ['count' => count($batch)]);
            // Fallback: row-by-row insertOrIgnore if upsert encounters conflict
            $success = 0;
            foreach ($batch as $row) {
                try {
                    $conn->table($table)->updateOrInsert(['id' => $row['id']], $row);
                    $success++;
                } catch (\Throwable $rowErr) {
                    // Skip invalid row
                }
            }
            return $success;
        }
    }

    /**
     * Map raw YAML item array to rAthena SQL table schema.
     */
    public function mapItemToSchema(array $item, array $validCols): array
    {
        $id = (int) ($item['Id'] ?? 0);
        if ($id <= 0) {
            return [];
        }

        $buy = isset($item['Buy']) ? (int) $item['Buy'] : null;
        $sell = isset($item['Sell']) ? (int) $item['Sell'] : ($buy !== null ? (int) floor($buy / 2) : null);
        if ($buy === null && $sell !== null) {
            $buy = $sell * 2;
        }

        $jobs = $item['Jobs'] ?? [];
        $isAllJobs = !empty($jobs['All']);

        $classes = $item['Classes'] ?? [];
        $isAllClasses = !empty($classes['All']);

        $loc = $item['Locations'] ?? [];
        $flags = $item['Flags'] ?? [];
        $delay = $item['Delay'] ?? [];
        $stack = $item['Stack'] ?? [];
        $nouse = $item['NoUse'] ?? [];
        $trade = $item['Trade'] ?? [];

        $record = [
            'id' => $id,
            'name_aegis' => (string) ($item['AegisName'] ?? ''),
            'name_english' => (string) ($item['Name'] ?? $item['AegisName'] ?? ''),
            'type' => (string) ($item['Type'] ?? 'Etc'),
            'subtype' => isset($item['SubType']) ? (string) $item['SubType'] : null,
            'price_buy' => $buy,
            'price_sell' => $sell,
            'weight' => isset($item['Weight']) ? (int) $item['Weight'] : 0,
            'attack' => isset($item['Attack']) ? (int) $item['Attack'] : 0,
            'defense' => isset($item['Defense']) ? (int) $item['Defense'] : 0,
            'range' => isset($item['Range']) ? (int) $item['Range'] : 0,
            'slots' => isset($item['Slots']) ? (int) $item['Slots'] : 0,

            // Jobs
            'job_all' => $isAllJobs ? 1 : 0,
            'job_novice' => ($isAllJobs || !empty($jobs['Novice'])) ? 1 : 0,
            'job_swordman' => ($isAllJobs || !empty($jobs['Swordman'])) ? 1 : 0,
            'job_mage' => ($isAllJobs || !empty($jobs['Mage'])) ? 1 : 0,
            'job_archer' => ($isAllJobs || !empty($jobs['Archer'])) ? 1 : 0,
            'job_acolyte' => ($isAllJobs || !empty($jobs['Acolyte'])) ? 1 : 0,
            'job_merchant' => ($isAllJobs || !empty($jobs['Merchant'])) ? 1 : 0,
            'job_thief' => ($isAllJobs || !empty($jobs['Thief'])) ? 1 : 0,
            'job_knight' => ($isAllJobs || !empty($jobs['Knight'])) ? 1 : 0,
            'job_priest' => ($isAllJobs || !empty($jobs['Priest'])) ? 1 : 0,
            'job_wizard' => ($isAllJobs || !empty($jobs['Wizard'])) ? 1 : 0,
            'job_blacksmith' => ($isAllJobs || !empty($jobs['Blacksmith'])) ? 1 : 0,
            'job_hunter' => ($isAllJobs || !empty($jobs['Hunter'])) ? 1 : 0,
            'job_assassin' => ($isAllJobs || !empty($jobs['Assassin'])) ? 1 : 0,
            'job_crusader' => ($isAllJobs || !empty($jobs['Crusader'])) ? 1 : 0,
            'job_monk' => ($isAllJobs || !empty($jobs['Monk'])) ? 1 : 0,
            'job_sage' => ($isAllJobs || !empty($jobs['Sage'])) ? 1 : 0,
            'job_rogue' => ($isAllJobs || !empty($jobs['Rogue'])) ? 1 : 0,
            'job_alchemist' => ($isAllJobs || !empty($jobs['Alchemist'])) ? 1 : 0,
            'job_barddancer' => ($isAllJobs || !empty($jobs['BardDancer']) || !empty($jobs['Bard']) || !empty($jobs['Dancer'])) ? 1 : 0,
            'job_supernovice' => ($isAllJobs || !empty($jobs['SuperNovice'])) ? 1 : 0,
            'job_gunslinger' => ($isAllJobs || !empty($jobs['Gunslinger'])) ? 1 : 0,
            'job_ninja' => ($isAllJobs || !empty($jobs['Ninja'])) ? 1 : 0,
            'job_taekwon' => ($isAllJobs || !empty($jobs['Taekwon'])) ? 1 : 0,
            'job_stargladiator' => ($isAllJobs || !empty($jobs['StarGladiator'])) ? 1 : 0,
            'job_soullinker' => ($isAllJobs || !empty($jobs['SoulLinker'])) ? 1 : 0,

            // Classes
            'class_all' => $isAllClasses ? 1 : 0,
            'class_normal' => ($isAllClasses || !empty($classes['Normal'])) ? 1 : 0,
            'class_upper' => ($isAllClasses || !empty($classes['Upper'])) ? 1 : 0,
            'class_baby' => ($isAllClasses || !empty($classes['Baby'])) ? 1 : 0,

            // Gender
            'gender' => isset($item['Gender']) ? (string) $item['Gender'] : 'Both',

            // Locations
            'location_head_top' => !empty($loc['Head_Top']) ? 1 : 0,
            'location_head_mid' => !empty($loc['Head_Mid']) ? 1 : 0,
            'location_head_low' => !empty($loc['Head_Low']) ? 1 : 0,
            'location_armor' => !empty($loc['Armor']) ? 1 : 0,
            'location_right_hand' => (!empty($loc['Right_Hand']) || !empty($loc['Both_Hand'])) ? 1 : 0,
            'location_left_hand' => (!empty($loc['Left_Hand']) || !empty($loc['Both_Hand'])) ? 1 : 0,
            'location_garment' => !empty($loc['Garment']) ? 1 : 0,
            'location_shoes' => !empty($loc['Shoes']) ? 1 : 0,
            'location_right_accessory' => (!empty($loc['Right_Accessory']) || !empty($loc['Both_Accessory'])) ? 1 : 0,
            'location_left_accessory' => (!empty($loc['Left_Accessory']) || !empty($loc['Both_Accessory'])) ? 1 : 0,
            'location_costume_head_top' => !empty($loc['Costume_Head_Top']) ? 1 : 0,
            'location_costume_head_mid' => !empty($loc['Costume_Head_Mid']) ? 1 : 0,
            'location_costume_head_low' => !empty($loc['Costume_Head_Low']) ? 1 : 0,
            'location_costume_garment' => !empty($loc['Costume_Garment']) ? 1 : 0,
            'location_ammo' => !empty($loc['Ammo']) ? 1 : 0,
            'location_shadow_armor' => !empty($loc['Shadow_Armor']) ? 1 : 0,
            'location_shadow_weapon' => !empty($loc['Shadow_Weapon']) ? 1 : 0,
            'location_shadow_shield' => !empty($loc['Shadow_Shield']) ? 1 : 0,
            'location_shadow_shoes' => !empty($loc['Shadow_Shoes']) ? 1 : 0,
            'location_shadow_right_accessory' => !empty($loc['Shadow_Right_Accessory']) ? 1 : 0,
            'location_shadow_left_accessory' => !empty($loc['Shadow_Left_Accessory']) ? 1 : 0,

            // Gear properties
            'weapon_level' => isset($item['WeaponLevel']) ? (int) $item['WeaponLevel'] : null,
            'armor_level' => isset($item['ArmorLevel']) ? (int) $item['ArmorLevel'] : null,
            'equip_level_min' => isset($item['EquipLevelMin']) ? (int) $item['EquipLevelMin'] : 0,
            'equip_level_max' => isset($item['EquipLevelMax']) ? (int) $item['EquipLevelMax'] : 0,
            'refineable' => !empty($item['Refineable']) ? 1 : 0,
            'view' => isset($item['View']) ? (int) $item['View'] : 0,
            'alias_name' => isset($item['AliasName']) ? (string) $item['AliasName'] : null,

            // Flags
            'flag_buyingstore' => !empty($flags['BuyingStore']) ? 1 : 0,
            'flag_deadbranch' => !empty($flags['DeadBranch']) ? 1 : 0,
            'flag_container' => !empty($flags['Container']) ? 1 : 0,
            'flag_uniqueid' => !empty($flags['UniqueId']) ? 1 : 0,
            'flag_bindonequip' => !empty($flags['BindOnEquip']) ? 1 : 0,
            'flag_dropannounce' => !empty($flags['DropAnnounce']) ? 1 : 0,
            'flag_noconsume' => !empty($flags['NoConsume']) ? 1 : 0,
            'flag_dropeffect' => isset($flags['DropEffect']) ? (string) $flags['DropEffect'] : null,

            // Delay & Stack
            'delay_duration' => isset($delay['Duration']) ? (int) $delay['Duration'] : null,
            'delay_status' => isset($delay['Status']) ? (string) $delay['Status'] : null,
            'stack_amount' => isset($stack['Amount']) ? (int) $stack['Amount'] : null,
            'stack_inventory' => isset($stack['Inventory']) ? ($stack['Inventory'] ? 1 : 0) : null,
            'stack_cart' => !empty($stack['Cart']) ? 1 : 0,
            'stack_storage' => !empty($stack['Storage']) ? 1 : 0,
            'stack_guildstorage' => !empty($stack['GuildStorage']) ? 1 : 0,

            // NoUse & Trade
            'nouse_override' => isset($nouse['Override']) ? (int) $nouse['Override'] : null,
            'nouse_sitting' => !empty($nouse['Sitting']) ? 1 : 0,
            'trade_override' => isset($trade['Override']) ? (int) $trade['Override'] : null,
            'trade_nodrop' => !empty($trade['NoDrop']) ? 1 : 0,
            'trade_notrade' => !empty($trade['NoTrade']) ? 1 : 0,
            'trade_tradepartner' => !empty($trade['TradePartner']) ? 1 : 0,
            'trade_nosell' => !empty($trade['NoSell']) ? 1 : 0,
            'trade_nocart' => !empty($trade['NoCart']) ? 1 : 0,
            'trade_nostorage' => !empty($trade['NoStorage']) ? 1 : 0,
            'trade_noguildstorage' => !empty($trade['NoGuildStorage']) ? 1 : 0,
            'trade_nomail' => !empty($trade['NoMail']) ? 1 : 0,
            'trade_noauction' => !empty($trade['NoAuction']) ? 1 : 0,

            // Scripts
            'script' => isset($item['Script']) ? (string) $item['Script'] : null,
            'equip_script' => isset($item['EquipScript']) ? (string) $item['EquipScript'] : null,
            'unequip_script' => isset($item['UnEquipScript']) ? (string) $item['UnEquipScript'] : null,
        ];

        // Filter out any columns not present in the game server's database table
        return array_intersect_key($record, $validCols);
    }
}

