from PIL import Image
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

for key, (pattern, out_name) in images.items():
    search_path = os.path.join(input_dir, pattern)
    files = glob.glob(search_path)
    if not files:
        print(f"Skipping {key}, file not found")
        continue

    files.sort(key=os.path.getmtime, reverse=True)
    in_file = files[0]
    out_file = os.path.join(output_dir, out_name)

    print(f"Processing {in_file} -> {out_file}")
    
    img = Image.open(in_file).convert("RGBA")
    datas = img.getdata()

    new_data = []
    # threshold for white
    for item in datas:
        # Check if pixel is white or very close to white
        if item[0] > 240 and item[1] > 240 and item[2] > 240:
            # Change all white (also shades of whites) to transparent
            new_data.append((255, 255, 255, 0))
        else:
            new_data.append(item)

    img.putdata(new_data)
    img.save(out_file, "PNG")
    print(f"Saved {out_name}")
