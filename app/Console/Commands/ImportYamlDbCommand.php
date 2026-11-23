<?php

namespace App\Console\Commands;

use App\Services\Database\ItemDbImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportYamlDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aether:import-yaml 
                            {file_or_url : Local filesystem path or remote URL to the YAML database file}
                            {--table=item_db : Target table name (item_db, item_db2)}
                            {--mode=upsert : Import mode: upsert, truncate, or insert_missing}
                            {--truncate : Shorthand to truncate the table before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import rAthena or Hercules YAML database files (e.g. item_db.yml, item_db_equip.yml) into the game database.';

    public function handle(ItemDbImporter $importer): int
    {
        $input = $this->argument('file_or_url');
        $table = $this->option('table') ?: 'item_db';
        $mode = $this->option('truncate') ? 'truncate' : ($this->option('mode') ?: 'upsert');

        $this->info("=== AetherCP Database & YAML Importer ===");
        $this->line("Target Table: <comment>{$table}</comment>");
        $this->line("Import Mode:  <comment>{$mode}</comment>");

        $tempFile = null;
        $filePath = $input;

        // If remote URL, download temporarily
        if (filter_var($input, FILTER_VALIDATE_URL)) {
            $this->info("Downloading remote YAML file from: {$input}");
            $response = Http::timeout(60)->get($input);

            if (!$response->successful()) {
                $this->error("Failed to download remote YAML: HTTP " . $response->status());
                return 1;
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'ro_yaml_');
            file_put_contents($tempFile, $response->body());
            $filePath = $tempFile;
            $this->info("Downloaded (" . number_format(strlen($response->body())) . " bytes).");
        }

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Parsing and streaming YAML records into '{$table}'...");
        $bar = null;

        try {
            $result = $importer->import(
                $filePath,
                $table,
                $mode,
                function ($processed, $imported) {
                    $this->output->write("\r<info>Processed:</info> {$processed} items | <info>Imported/Upserted:</info> {$imported}");
                }
            );

            $this->newLine();
            $this->info("--------------------------------------------------");
            $this->info("✓ Import Completed Successfully!");
            $this->line("  Items Processed: <comment>{$result['processed']}</comment>");
            $this->line("  Items Ingested:  <comment>{$result['imported']}</comment>");
            $this->line("  Execution Time:  <comment>{$result['elapsed_seconds']}s</comment>");
            $this->line("  Target Table:    <comment>{$result['table']}</comment>");
            if (!empty($result['header']['type'])) {
                $this->line("  YAML Header:     <comment>{$result['header']['type']} (v{$result['header']['version']})</comment>");
            }
            $this->info("--------------------------------------------------");

            return 0;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error("Import Failed: " . $e->getMessage());
            return 1;
        } finally {
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }
    }
}

