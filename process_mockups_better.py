from PIL import Image, ImageDraw, ImageFilter
import os
import glob

input_dir = r"C:\Users\ThinkPad\.gemini\antigravity\brain\04ab8af3-c5f8-4764-a1af-21a2716d1aa7"
output_dir = r"c:\laragon\www\percobaanskripsi_1\public\images\mockups"
os.makedirs(output_dir, exist_ok=True)

images = {
    "kaos_depan": ("kaos_depan_*.png", "kaos.png"),
    "kaos_belakang": ("kaos_belakang_*.png", "kaos_belakang.png"),
    "hoodie_depan": ("hoodie_depan_*.png", "hoodie.png"),
    "hoodie_belakang": ("hoodie_belakang_*.png", "hoodie_belakang.png"),
}

def remove_background(img_path, out_path):
    img = Image.open(img_path).convert("RGBA")
    gray = img.convert("L")
    
    # thresholding
    # Any pixel > 230 becomes 255 (white bg candidate), else 0 (dark)
    bg_mask = gray.point(lambda p: 255 if p > 235 else 0)
    
    # floodfill outer background
    # Fill from corners to ensure we get all surrounding background
    width, height = bg_mask.size
    ImageDraw.floodfill(bg_mask, (0, 0), 128)
    ImageDraw.floodfill(bg_mask, (width-1, 0), 128)
    ImageDraw.floodfill(bg_mask, (0, height-1), 128)
    ImageDraw.floodfill(bg_mask, (width-1, height-1), 128)
    
    # Build alpha channel
    # Anything that is 128 (outer background) gets 0 opacity, else 255 opacity
    alpha = bg_mask.point(lambda p: 0 if p == 128 else 255)
    
    # Slight blur to reduce jagged edges (bercak)
    alpha = alpha.filter(ImageFilter.GaussianBlur(1))
    
    img.putalpha(alpha)
    img.save(out_path, "PNG")
    print(f"Processed: {out_path}")

for key, (pattern, out_name) in images.items():
    search_path = os.path.join(input_dir, pattern)
    files = glob.glob(search_path)
    if not files:
        continue
    files.sort(key=os.path.getmtime, reverse=True)
    remove_background(files[0], os.path.join(output_dir, out_name))
