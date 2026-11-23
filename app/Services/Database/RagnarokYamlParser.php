<?php

namespace App\Services\Database;

/**
 * High-performance streaming YAML parser specifically tailored for rAthena & Hercules
 * game database files (ITEM_DB, MOB_DB, etc.).
 *
 * Designed to process large 40,000+ line YAML files with minimal memory footprint (O(1) memory),
 * yielding parsed item structures one at a time.
 */
class RagnarokYamlParser
{
    /**
     * Inspect file header without loading the entire file.
     *
     * @param string $filePath
     * @return array{type: ?string, version: ?int, imports: array}
     */
    public function inspectHeader(string $filePath): array
    {
        $handle = @fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Unable to open YAML file: {$filePath}");
        }

        $header = ['type' => null, 'version' => null, 'imports' => []];
        $inHeader = false;
        $inFooter = false;
        $inImports = false;
        $linesChecked = 0;

        while (($line = fgets($handle)) !== false && $linesChecked < 300) {
            $linesChecked++;
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            if (preg_match('/^Header:\s*$/i', $trimmed)) {
                $inHeader = true;
                $inFooter = false;
                continue;
            }

            if (preg_match('/^Body:\s*$/i', $trimmed)) {
                $inHeader = false;
                break; // Stop once Body begins to preserve speed
            }

            if ($inHeader) {
                if (preg_match('/^Type:\s*([A-Za-z0-9_-]+)/i', $trimmed, $m)) {
                    $header['type'] = strtoupper(trim($m[1]));
                } elseif (preg_match('/^Version:\s*(\d+)/i', $trimmed, $m)) {
                    $header['version'] = (int) $m[1];
                }
            }
        }

        fclose($handle);
        return $header;
    }

    /**
     * Stream items from a rAthena YAML file yielding parsed associative arrays.
     *
     * @param string $filePath
     * @return \Generator<int, array>
     */
    public function streamItems(string $filePath): \Generator
    {
        $handle = @fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Unable to open YAML file for streaming: {$filePath}");
        }

        $inBody = false;
        $currentItemLines = [];
        $itemCount = 0;

        while (($line = fgets($handle)) !== false) {
            $rawLine = rtrim($line, "\r\n");
            $trimmed = trim($rawLine);

            // Skip comments and blank lines outside items
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            // Check for Body: section
            if (!$inBody) {
                if (preg_match('/^Body:\s*$/i', $trimmed)) {
                    $inBody = true;
                }
                continue;
            }

            // Check for Footer: section (end of Body)
            if (preg_match('/^Footer:\s*$/i', $trimmed)) {
                break;
            }

            // Detect start of a new item entry (e.g. "  - Id: 1101" or "- Id: 1101")
            if (preg_match('/^\s*-\s+Id:\s*(\d+)/i', $rawLine)) {
                if (!empty($currentItemLines)) {
                    $parsed = $this->parseSingleItemLines($currentItemLines);
                    if ($parsed && isset($parsed['Id'])) {
                        $itemCount++;
                        yield $parsed;
                    }
                    $currentItemLines = [];
                }
            }

            $currentItemLines[] = $rawLine;
        }

        // Yield final item
        if (!empty($currentItemLines)) {
            $parsed = $this->parseSingleItemLines($currentItemLines);
            if ($parsed && isset($parsed['Id'])) {
                yield $parsed;
            }
        }

        fclose($handle);
    }

    /**
     * Parse the raw line block of a single item into a nested associative array.
     *
     * @param array<string> $lines
     * @return array<string, mixed>
     */
    public function parseSingleItemLines(array $lines): array
    {
        $item = [];
        $currentSection = null;
        $sectionIndent = 0;
        $multilineKey = null;
        $multilineIndent = 0;
        $multilineBuffer = [];

        foreach ($lines as $line) {
            // Trim leading dash for first line ("  - Id: 1101" -> "Id: 1101")
            $cleanLine = preg_replace('/^\s*-\s+/', '    ', $line);
            $trimmed = trim($cleanLine);

            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            // Calculate indentation level
            preg_match('/^(\s*)/', $cleanLine, $indentMatch);
            $indent = strlen($indentMatch[1] ?? '');

            // Handle multiline continuation (Script, EquipScript, etc.)
            if ($multilineKey !== null) {
                if ($indent > $multilineIndent || str_starts_with($trimmed, 'bonus') || str_starts_with($trimmed, '{') || str_starts_with($trimmed, '}')) {
                    $multilineBuffer[] = $trimmed;
                    continue;
                } else {
                    // Finished multiline
                    $item[$multilineKey] = implode("\n", $multilineBuffer);
                    $multilineKey = null;
                    $multilineIndent = 0;
                    $multilineBuffer = [];
                }
            }

            // If we are currently in a nested section, check if this line popped out of it
            if ($currentSection !== null && $indent <= $sectionIndent) {
                $currentSection = null;
                $sectionIndent = 0;
            }

            // Check for multiline script start: "Script: >" or "Script: |" or "EquipScript: >"
            if (preg_match('/^([A-Za-z0-9_]+):\s*[>|]\s*$/', $trimmed, $m)) {
                $multilineKey = $m[1];
                $multilineIndent = $indent;
                $multilineBuffer = [];
                $currentSection = null;
                continue;
            }

            // Check for nested section headers (e.g. "Jobs:", "Locations:", "Flags:", "Trade:", "Stack:", "Classes:")
            if (preg_match('/^([A-Za-z0-9_]+):\s*$/', $trimmed, $m)) {
                $sectionName = $m[1];
                $currentSection = $sectionName;
                $sectionIndent = $indent;
                if (!isset($item[$currentSection])) {
                    $item[$currentSection] = [];
                }
                continue;
            }

            // Check for key: value pairs
            if (preg_match('/^([A-Za-z0-9_]+):\s*(.*)$/', $trimmed, $m)) {
                $key = $m[1];
                $rawVal = trim($m[2]);
                $val = $this->castValue($rawVal);

                if ($currentSection !== null && $indent > $sectionIndent) {
                    $item[$currentSection][$key] = $val;
                } else {
                    $currentSection = null;
                    $sectionIndent = 0;
                    $item[$key] = $val;
                }
            }
        }

        // Flush any pending multiline buffer
        if ($multilineKey !== null && !empty($multilineBuffer)) {
            $item[$multilineKey] = implode("\n", $multilineBuffer);
        }

        return $item;
    }

    /**
     * Cast raw YAML scalar string into PHP typed value.
     */
    protected function castValue(string $val): mixed
    {
        $lower = strtolower($val);
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
        if ($lower === 'null' || $val === '~' || $val === '') return null;
        if (is_numeric($val)) {
            return str_contains($val, '.') ? (float) $val : (int) $val;
        }

        // Strip surrounding quotes if present
        if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
            (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
            return substr($val, 1, -1);
        }

        return $val;
    }
}
