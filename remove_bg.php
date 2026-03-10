<?php
function processMockup($input, $output) {
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

    // To remove the white background but keep the white shirt, we flood fill from the corners.
    // However, imagefill in PHP is strict on color. 
    // We can do a manual tolerance check.
    
    // Create a mask array
    $mask = array_fill(0, $w * $h, 0);
    $stack = [[0,0], [$w-1,0], [0,$h-1], [$w-1,$h-1]];
    
    while(count($stack) > 0) {
        $pt = array_pop($stack);
        $x = $pt[0]; $y = $pt[1];
        if($x < 0 || $x >= $w || $y < 0 || $y >= $h) continue;
        $idx = $y * $w + $x;
        if($mask[$idx] == 1) continue;
        
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        // Tolerance for white background
        if($r > 230 && $g > 230 && $b > 230) {
            $mask[$idx] = 1;
            $stack[] = [$x+1, $y];
            $stack[] = [$x-1, $y];
            $stack[] = [$x, $y+1];
            $stack[] = [$x, $y-1];
        }
    }

    for($y=0; $y<$h; $y++){
        for($x=0; $x<$w; $x++){
            $idx = $y * $w + $x;
            if($mask[$idx] == 1) {
                imagesetpixel($out, $x, $y, $trans);
            } else {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                // If it's a pixel on the edge of the mask, feathering could help, but let's just copy
                $color = imagecolorallocatealpha($out, $r, $g, $b, 0);
                imagesetpixel($out, $x, $y, $color);
            }
        }
    }
    imagepng($out, $output);
    imagedestroy($img);
    imagedestroy($out);
    echo "Saved $output\n";
}

@mkdir('public/images/mockups', 0777, true);
processMockup('C:\Users\ThinkPad\.gemini\antigravity\brain\d80313f1-5dee-4b11-b2af-084c7460892b\mockup_kaos_1773065944547.png', 'public/images/mockups/kaos.png');
processMockup('C:\Users\ThinkPad\.gemini\antigravity\brain\d80313f1-5dee-4b11-b2af-084c7460892b\mockup_hoodie_1773065964630.png', 'public/images/mockups/hoodie.png');
