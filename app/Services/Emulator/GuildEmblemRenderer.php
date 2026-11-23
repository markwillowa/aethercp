<?php

namespace App\Services\Emulator;

class GuildEmblemRenderer
{
    /**
     * Decode Ragnarok Online guild emblem raw data (gzip BMP) and render as transparent PNG.
     * In Ragnarok, magenta (#FF00FF) indicates transparent background.
     */
    public static function renderPng(?string $rawData): ?string
    {
        if (empty($rawData)) {
            return null;
        }

        $bmpData = null;

        // 1. Check if compressed with GZIP (\x1f\x8b)
        if (str_starts_with($rawData, "\x1f\x8b")) {
            $decompressed = @gzdecode($rawData);
            if ($decompressed) {
                $bmpData = $decompressed;
            } else {
                // Fallback to gzinflate
                $decompressed = @gzinflate(substr($rawData, 10, -8));
                if ($decompressed) {
                    $bmpData = $decompressed;
                }
            }
        }
        // 2. Check if zlib compressed (78 9c or 78 01)
        elseif (str_starts_with($rawData, "\x78\x9c") || str_starts_with($rawData, "\x78\x01")) {
            $decompressed = @gzuncompress($rawData);
            if ($decompressed) {
                $bmpData = $decompressed;
            }
        }
        // 3. Raw BMP
        elseif (str_starts_with($rawData, 'BM')) {
            $bmpData = $rawData;
        }

        if (empty($bmpData)) {
            return null;
        }

        // Create GD image from decompressed BMP string
        $img = @imagecreatefromstring($bmpData);
        if (!$img) {
            return null;
        }

        // Ensure 24x24 dimensions
        $width = imagesx($img);
        $height = imagesy($img);

        // Create truecolor canvas with alpha channel
        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        $transparentColor = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $transparentColor);

        // Scan pixels: if color is magenta (R:255, G:0, B:255), make transparent
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                if ($r === 255 && $g === 0 && $b === 255) {
                    imagesetpixel($canvas, $x, $y, $transparentColor);
                } else {
                    $color = imagecolorallocatealpha($canvas, $r, $g, $b, 0);
                    imagesetpixel($canvas, $x, $y, $color);
                }
            }
        }

        imagedestroy($img);

        ob_start();
        imagepng($canvas);
        $pngOutput = ob_get_clean();
        imagedestroy($canvas);

        return $pngOutput;
    }

    /**
     * Render a fallback placeholder emblem for guilds without an emblem.
     */
    public static function renderPlaceholder(): string
    {
        $canvas = imagecreatetruecolor(24, 24);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        $bg = imagecolorallocatealpha($canvas, 15, 23, 42, 0); // Dark slate
        imagefilledrectangle($canvas, 0, 0, 24, 24, $bg);

        $border = imagecolorallocatealpha($canvas, 14, 116, 144, 0); // Cyan border
        imagerectangle($canvas, 0, 0, 23, 23, $border);

        ob_start();
        imagepng($canvas);
        $png = ob_get_clean();
        imagedestroy($canvas);

        return $png;
    }
}

