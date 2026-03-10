<?php
function removeBackgroundStrict($input, $output) {
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

    $mask = array_fill(0, $w * $h, 0); 
    $stack = [[0,0], [$w-1,0], [0,$h-1], [$w-1,$h-1]];
    
    // Very aggressive threshold to eat through the anti-aliasing white halo
    // The t-shirts are dark grey/black, so white/grey halo pixels (e.g. RGB 130,130,130) will be counted as background.
    while(count($stack) > 0) {
        $pt = array_pop($stack);
        $x = $pt[0]; $y = $pt[1];
        if($x < 0 || $x >= $w || $y < 0 || $y >= $h) continue;
        $idx = $y * $w + $x;
        if($mask[$idx] == 1) continue;
        
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF; $g = ($rgb >> 8) & 0xFF; $b = $rgb & 0xFF;
        
        if($r > 110 && $g > 110 && $b > 110) {
            $mask[$idx] = 1;
            $stack[] = [$x+1, $y]; $stack[] = [$x-1, $y];
            $stack[] = [$x, $y+1]; $stack[] = [$x, $y-1];
        }
    }

    // Pass 2: Feathering (soft edge)
    // To make sure the new border isn't a hard jagged 1-bit line, we blur the edges by checking neighboring background pixels.
    for($y=0; $y<$h; $y++){
        for($x=0; $x<$w; $x++){
            $idx = $y * $w + $x;
            if($mask[$idx] == 1) {
                imagesetpixel($out, $x, $y, $trans);
            } else {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF; $g = ($rgb >> 8) & 0xFF; $b = $rgb & 0xFF;
                
                // Find distance to background
                $dist = 9;
                for($dy=-3; $dy<=3; $dy++){
                    for($dx=-3; $dx<=3; $dx++){
                        $nx = $x + $dx; $ny = $y + $dy;
                        if($nx>=0 && $nx<$w && $ny>=0 && $ny<$h){
                            if($mask[$ny*$w+$nx] == 1) {
                                $d = sqrt($dx*$dx + $dy*$dy);
                                if($d < $dist) $dist = $d;
                            }
                        }
                    }
                }

                if($dist < 2.5) {
                    // Soften the edge pixels
                    $alpha = 127 - (int)($dist * 50); // dist=1 -> 77, dist=2 -> 27
                    if($alpha < 0) $alpha = 0; if($alpha > 127) $alpha = 127;
                    $color = imagecolorallocatealpha($out, $r, $g, $b, $alpha);
                    imagesetpixel($out, $x, $y, $color);
                } else {
                    $color = imagecolorallocatealpha($out, $r, $g, $b, 0); // Opaque
                    imagesetpixel($out, $x, $y, $color);
                }
            }
        }
    }
    
    imagepng($out, $output);
    imagedestroy($img);
    imagedestroy($out);
    echo "Saved $output\n";
}

@mkdir('public/images/mockups', 0777, true);
removeBackgroundStrict('C:\Users\ThinkPad\.gemini\antigravity\brain\d80313f1-5dee-4b11-b2af-084c7460892b\mockup_kaos_1773065944547.png', 'public/images/mockups/kaos.png');
removeBackgroundStrict('C:\Users\ThinkPad\.gemini\antigravity\brain\d80313f1-5dee-4b11-b2af-084c7460892b\mockup_hoodie_1773065964630.png', 'public/images/mockups/hoodie.png');
