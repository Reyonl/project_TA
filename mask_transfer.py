from PIL import Image, ImageOps
import os
import glob

input_dir = r"C:\Users\ThinkPad\.gemini\antigravity\brain\04ab8af3-c5f8-4764-a1af-21a2716d1aa7"
output_dir = r"c:\laragon\www\percobaanskripsi_1\public\images\mockups"

def transfer_mask(original_path, generated_pattern, output_path, flip_mask=True):
    # Load original perfect masked image
    if not os.path.exists(original_path):
        print(f"Skipping {original_path}, original not found")
        return
        
    orig_img = Image.open(original_path).convert("RGBA")
    
    # Extract alpha from original
    r, g, b, orig_alpha = orig_img.split()
    
    # If it's the back, we technically flip the mask horizontally 
    # because the left sleeve on the front is the right sleeve on the back.
    if flip_mask:
        orig_alpha = ImageOps.mirror(orig_alpha)
        
    # Load AI generated back image
    search_path = os.path.join(input_dir, generated_pattern)
    files = glob.glob(search_path)
    if not files:
        print(f"Skipping {generated_pattern}, file not found")
        return
        
    files.sort(key=os.path.getmtime, reverse=True)
    gen_img = Image.open(files[0]).convert("RGB")
    
    # Resize generated image to exactly match the original dimensions
    # We use crop + resize or just forced resize.
    gen_img = gen_img.resize(orig_img.size, Image.Resampling.LANCZOS)
    
    # Apply perfect mask
    gen_img.putalpha(orig_alpha)
    
    # Save output
    gen_img.save(output_path, "PNG")
    print(f"Successfully created {output_path} with perfect mask")

# Process Kaos Belakang
transfer_mask(
    os.path.join(output_dir, "kaos.png"), 
    "kaos_belakang_*.png", 
    os.path.join(output_dir, "kaos_belakang.png")
)

# Process Hoodie Belakang
transfer_mask(
    os.path.join(output_dir, "hoodie.png"), 
    "hoodie_belakang_*.png", 
    os.path.join(output_dir, "hoodie_belakang.png")
)
