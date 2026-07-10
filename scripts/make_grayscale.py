import cv2

for path in ['public/images/mockups/polo.png', 'public/images/mockups/polo_belakang.png']:
    img = cv2.imread(path, cv2.IMREAD_UNCHANGED)
    if img is None:
        print(f"Could not read {path}")
        continue
    if len(img.shape) < 3 or img.shape[2] != 4:
        print(f"Skipping {path}, not a transparent PNG")
        continue
    
    bgr = img[:, :, :3]
    alpha = img[:, :, 3]
    
    gray = cv2.cvtColor(bgr, cv2.COLOR_BGR2GRAY)
    gray_bgr = cv2.merge([gray, gray, gray])
    
    final = cv2.cvtColor(gray_bgr, cv2.COLOR_BGR2BGRA)
    final[:, :, 3] = alpha
    
    cv2.imwrite(path, final)
    print(f"Berhasil mengkonversi {path} ke template grayscale.")
