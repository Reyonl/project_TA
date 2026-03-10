<?php
$file = 'C:\Users\ThinkPad\.gemini\antigravity\brain\d80313f1-5dee-4b11-b2af-084c7460892b\mockup_kaos_1773065944547.png';
$img = imagecreatefrompng($file);
$w = imagesx($img);
$h = imagesy($img);

function c($img, $x, $y) {
    $rgb = imagecolorat($img, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    return "($r,$g,$b)";
}

$output = "Top-Left: " . c($img, 0, 0) . "\n";
$output .= "Top-Right: " . c($img, $w-1, 0) . "\n";
$output .= "Center: " . c($img, (int)($w/2), (int)($h/2)) . "\n";
$output .= "Center-Left: " . c($img, (int)($w/4), (int)($h/2)) . "\n";
$output .= "Bottom-Left: " . c($img, 0, $h-1) . "\n";
$output .= "Bottom-Right: " . c($img, $w-1, $h-1) . "\n";

file_put_contents('color_debug.txt', $output);
echo "Done.";
