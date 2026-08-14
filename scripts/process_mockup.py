import cv2
import numpy as np
import os
import argparse
import sys

def refine_mockup_perfect(original_path, output_path, model_name="sam"):
    if not os.path.exists(original_path):
        print(f"Error: File '{original_path}' tidak ditemukan.")
        sys.exit(1)

    print(f"Membaca gambar mentah dari: {original_path}...")
    
    # 1. Baca gambar BGR asli untuk mempertahankan warna/bayangan asli
    orig_img = cv2.imread(original_path)
    if orig_img is None:
        print("Gagal membaca gambar mentah.")
        sys.exit(1)
        
    bgr_orig = orig_img[:, :, :3]
    
    # Konversi ke template grayscale yang akan dikalikan warna secara dinamis di web
    print("Mengonversi ke template Grayscale...")
    gray = cv2.cvtColor(bgr_orig, cv2.COLOR_BGR2GRAY)
    grayscale_bgr = cv2.merge([gray, gray, gray])
    
    # 2. Jalankan rembg untuk mendapatkan mask alpha outline yang sempurna
    try:
        from rembg import remove, new_session
    except ImportError:
        print("Error: Library 'rembg' tidak terinstall. Jalankan 'pip install rembg[onnxruntime]'")
        sys.exit(1)
        
    with open(original_path, 'rb') as i:
        input_data = i.read()
    
    print(f"Menjalankan AI Background Removal (Model: {model_name})...")
    try:
        session = new_session(model_name)
        output_data = remove(input_data, session=session)
    except Exception as e:
        print("Pemrosesan model AI gagal:", e)
        sys.exit(1)
        
    nparr = np.frombuffer(output_data, np.uint8)
    rgba_rembg = cv2.imdecode(nparr, cv2.IMREAD_UNCHANGED)
    alpha_sam = rgba_rembg[:, :, 3]
    
    print("Menambal lubang (holes) di dalam area pakaian...")
    _, thresh = cv2.threshold(alpha_sam, 128, 255, cv2.THRESH_BINARY)
    contours, hierarchy = cv2.findContours(thresh, cv2.RETR_CCOMP, cv2.CHAIN_APPROX_NONE)
    
    holes_mask = np.zeros_like(alpha_sam)
    if hierarchy is not None:
        for i in range(len(contours)):
            # Jika kontur memiliki parent (berarti ini adalah lubang di dalam objek utama)
            if hierarchy[0][i][3] != -1:
                cv2.drawContours(holes_mask, contours, i, 255, thickness=cv2.FILLED)
                
    # Gabungkan lubang yang ditambal kembali ke mask SAM asli
    final_alpha = cv2.add(alpha_sam, holes_mask)
        
    # 4. Gabungkan BGR grayscale asli dengan Alpha Mask yang sudah di-perfect-kan
    print("Menyimpan hasil akhir...")
    final_rgba = cv2.cvtColor(grayscale_bgr, cv2.COLOR_BGR2BGRA)
    final_rgba[:, :, 3] = final_alpha
    
    os.makedirs(os.path.dirname(os.path.abspath(output_path)), exist_ok=True)
    cv2.imwrite(output_path, final_rgba)
    print(f"Selesai! Mockup berhasil diproses dan disimpan ke: {output_path}")

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Script Standar untuk Pembersihan Background Mockup Baju secara Sempurna")
    parser.add_argument("input", help="Path gambar mentah (original) yang akan diproses")
    parser.add_argument("output", help="Path tujuan untuk menyimpan hasil (contoh: public/images/mockups/baju.png)")
    parser.add_argument("--model", default="sam", help="Model AI yang digunakan (default: sam)")
    
    args = parser.parse_args()
    refine_mockup_perfect(args.input, args.output, args.model)
