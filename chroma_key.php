<?php
function chromaKeyGreen($input, $output) {
    if(!file_exists($input)) {
        echo "File $input not found\n";
        return;
    }
    $img = imagecreatefrompng($input);
    $w = imagesx($img);
    $h = imagesy($img);
    $out = imagecreatetruecolor($w, $h);
    imagesavealpha($out, true);
    $trans = imagecolorallocatealpha($out, 0,0,0,127);
    imagefill($out, 0,0, $trans);

    for($y=0; $y<$h; $y++){
        for($x=0; $x<$w; $x++){
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            // Background is pure green. So we check if Green is dominant and R/B are low.
            // Typical green screen might cast some shadow, so we use a ratio or threshold.
            if($g > 150 && $r < 120 && $b < 120) {
                // It's green background -> transparent
                imagesetpixel($out, $x, $y, $trans);
            } else if ($g > $r + 30 && $g > $b + 30) {
                // Secondary check for darker green shadows
                // Calculate how "green" it is to make a soft alpha transition
                $alpha = 127 - (int)((255 - $g) / 2); // heuristic
                if($alpha < 0) $alpha = 0; if($alpha > 127) $alpha = 127;
                $color = imagecolorallocatealpha($out, $r, $g, $b, $alpha);
                imagesetpixel($out, $x, $y, $color);
            }
            else {
                // Keep the original pixel
                $color = imagecolorallocatealpha($out, $r, $g, $b, 0);
                imagesetpixel($out, $x, $y, $color);
            }
        }
    }
    
    // Scale down image to save some bandwidth because original generated image is huge (1024x1024)
    // We only need ~600px max
    $newW = 600;
    $newH = (int)($h * ($newW / $w));
    $resized = imagecreatetruecolor($newW, $newH);
    imagesavealpha($resized, true);
    imagefill($resized, 0,0, $trans);
    imagecopyresampled($resized, $out, 0, 0, 0, 0, $newW, $newH, $w, $h);

    imagepng($resized, $output);
    imagedestroy($img);
    imagedestroy($out);
    imagedestroy($resized);
    echo "Saved $output\n";
}

@mkdir('public/images/mockups', 0777, true);
chromaKeyGreen('C:\Users\ThinkPad\.gemini\antigravity\brain\d80313f1-5dee-4b11-b2af-084c7460892b\green_screen_tshirt_1773097153841.png', 'public/images/mockups/kaos.png');
chromaKeyGreen('C:\Users\ThinkPad\.gemini\antigravity\brain\d80313f1-5dee-4b11-b2af-084c7460892b\green_screen_hoodie_1773097173913.png', 'public/images/mockups/hoodie.png');
