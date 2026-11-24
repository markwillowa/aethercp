<?php
// Flood-fill background remover for Angeling sprite

$imgDir = __DIR__ . '/../themes/aether-chronicles/assets/images';

// We start from angeling_card.png or angeling_status_badge.jpg/png or original avatar
// Let's check which source has the best Angeling
$src = imagecreatefrompng("$imgDir/angeling_avatar.png");
$w = imagesx($src);
$h = imagesy($src);

// If angeling_avatar.png was already overwritten, let's also check angeling_card.png
// In angeling_card.png, the card has an Angeling drawing.
// But let's check angeling_avatar.png - it's the 153x139 sprite.
// Let's create an array of visited/transparent pixels
$transparentMap = array_fill(0, $w, array_fill(0, $h, false));

// Flood fill queue from all edges
$queue = [];
for ($x = 0; $x < $w; $x++) {
    $queue[] = [$x, 0];
    $queue[] = [$x, $h - 1];
}
for ($y = 0; $y < $h; $y++) {
    $queue[] = [0, $y];
    $queue[] = [$w - 1, $y];
}

// Function to check if a pixel is "background" (light blue, light cyan, white, light grey)
// Angeling is bright pink (#ff6b8b, #ff8da1), gold halo (#ffd700), white wings with dark outlines.
function isBackgroundPixel($c) {
    $r = ($c >> 16) & 0xFF;
    $g = ($c >> 8) & 0xFF;
    $b = $c & 0xFF;
    $a = ($c >> 24) & 0x7F;

    // Already transparent
    if ($a > 50) return true;

    // If it's light sky blue / white (r > 190, g > 200, b > 210)
    if ($b > 195 && $g > 190 && $r > 180) return true;
    if ($r > 215 && $g > 215 && $b > 215) return true;
    
    // Very light grey or cyan
    if (abs($r - $g) < 25 && abs($g - $b) < 25 && $r > 190) return true;

    return false;
}

$visited = array_fill(0, $w, array_fill(0, $h, false));
$qIndex = 0;

while ($qIndex < count($queue)) {
    [$qx, $qy] = $queue[$qIndex++];

    if ($qx < 0 || $qx >= $w || $qy < 0 || $qy >= $h) continue;
    if ($visited[$qx][$qy]) continue;
    $visited[$qx][$qy] = true;

    $c = imagecolorat($src, $qx, $qy);
    if (isBackgroundPixel($c)) {
        $transparentMap[$qx][$qy] = true;
        // add 4-way neighbors
        $queue[] = [$qx + 1, $qy];
        $queue[] = [$qx - 1, $qy];
        $queue[] = [$qx, $qy + 1];
        $queue[] = [$qx, $qy - 1];
    }
}

// Build destination image
$dst = imagecreatetruecolor($w, $h);
imagesavealpha($dst, true);
imagealphablending($dst, false);
$clear = imagecolorallocatealpha($dst, 0, 0, 0, 127);
imagefill($dst, 0, 0, $clear);

for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        if ($transparentMap[$x][$y]) {
            imagesetpixel($dst, $x, $y, $clear);
        } else {
            $c = imagecolorat($src, $x, $y);
            $r = ($c >> 16) & 0xFF;
            $g = ($c >> 8) & 0xFF;
            $b = $c & 0xFF;

            // Check if near a transparent neighbor for anti-aliasing
            $hasTransNeighbor = false;
            foreach ([[-1,0],[1,0],[0,-1],[0,1]] as [$dx,$dy]) {
                $nx = $x + $dx; $ny = $y + $dy;
                if ($nx >= 0 && $nx < $w && $ny >= 0 && $ny < $h && $transparentMap[$nx][$ny]) {
                    $hasTransNeighbor = true;
                    break;
                }
            }

            if ($hasTransNeighbor && ($r > 180 && $g > 180 && $b > 180)) {
                $col = imagecolorallocatealpha($dst, $r, $g, $b, 70);
            } else {
                $col = imagecolorallocatealpha($dst, $r, $g, $b, 0);
            }
            imagesetpixel($dst, $x, $y, $col);
        }
    }
}

// Save to destination
imagepng($dst, "$imgDir/angeling_transparent.png");
imagepng($dst, "$imgDir/angeling_avatar.png");
imagepng($dst, "$imgDir/angeling_mascot_transparent.png");
imagepng($dst, "$imgDir/mascot_angeling.png");

echo "Flood-fill transparency complete! Saved clean transparent Angeling sprites.\n";
