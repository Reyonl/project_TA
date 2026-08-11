<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreDesainRequest;
use Illuminate\Http\Request;

use App\Models\Desain;
use App\Models\Template;
use App\Models\Produk;
use App\Models\Cart;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    public function index(Request $request, Produk $produk)
    {
        $templates = Template::all();
        $desainRevisi = null;
        if ($request->has('revisi')) {
            $desainRevisi = Desain::find($request->revisi);
            // Verify ownership
            if ($desainRevisi && $desainRevisi->id_customer !== auth()->guard('customer')->id()) {
                abort(403, 'Unauthorized action.');
            }
        }
        return view('customer.designs.editor', compact('templates', 'produk', 'desainRevisi'));
    }

    public function store(StoreDesainRequest $request)
    {
        $validated = $request->validated();

        $base64_image = $request->file_desain;
        
        // Memisahkan data MIME dan ekstensi dari data base64 sebenarnya (Depan)
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
            $image_base64 = substr($base64_image, strpos($base64_image, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, jpeg, gif
            
            // ... (validation type)
            $image_base64 = str_replace(' ', '+', $image_base64);
            $image_data = base64_decode($image_base64);
        }

        $fileName = 'designs/' . Str::random(40) . '.' . $type;
        Storage::disk('public')->put($fileName, $image_data);

        // Memproses desain belakang jika ada
        $fileNameBelakang = null;
        if ($request->filled('file_desain_belakang')) {
            $base64_image_belakang = $request->file_desain_belakang;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image_belakang, $typeBelakang)) {
                $image_base64_b = substr($base64_image_belakang, strpos($base64_image_belakang, ',') + 1);
                $extBelakang = strtolower($typeBelakang[1]);
                $image_base64_b = str_replace(' ', '+', $image_base64_b);
                $image_data_b = base64_decode($image_base64_b);
                
                $fileNameBelakang = 'designs/' . Str::random(40) . '-back.' . $extBelakang;
                Storage::disk('public')->put($fileNameBelakang, $image_data_b);
            }
        }

        // Memproses desain kiri jika ada
        $fileNameKiri = null;
        if ($request->filled('file_desain_kiri')) {
            $base64_image_kiri = $request->file_desain_kiri;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image_kiri, $typeKiri)) {
                $image_base64_k = substr($base64_image_kiri, strpos($base64_image_kiri, ',') + 1);
                $extKiri = strtolower($typeKiri[1]);
                $image_base64_k = str_replace(' ', '+', $image_base64_k);
                $image_data_k = base64_decode($image_base64_k);
                
                $fileNameKiri = 'designs/' . Str::random(40) . '-left.' . $extKiri;
                Storage::disk('public')->put($fileNameKiri, $image_data_k);
            }
        }

        // Memproses desain kanan jika ada
        $fileNameKanan = null;
        if ($request->filled('file_desain_kanan')) {
            $base64_image_kanan = $request->file_desain_kanan;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image_kanan, $typeKanan)) {
                $image_base64_kn = substr($base64_image_kanan, strpos($base64_image_kanan, ',') + 1);
                $extKanan = strtolower($typeKanan[1]);
                $image_base64_kn = str_replace(' ', '+', $image_base64_kn);
                $image_data_kn = base64_decode($image_base64_kn);
                
                $fileNameKanan = 'designs/' . Str::random(40) . '-right.' . $extKanan;
                Storage::disk('public')->put($fileNameKanan, $image_data_kn);
            }
        }

        // Memproses Raw Assets (Gambar Mentah)
        $rawAssetsPaths = [];
        if ($request->has('raw_assets') && is_array($request->raw_assets)) {
            foreach ($request->raw_assets as $rawBase64) {
                if (preg_match('/^data:image\/(\w+);base64,/', $rawBase64, $typeRaw)) {
                    $rawImgData = substr($rawBase64, strpos($rawBase64, ',') + 1);
                    $extRaw = strtolower($typeRaw[1]);
                    $rawImgData = str_replace(' ', '+', $rawImgData);
                    $decodedRaw = base64_decode($rawImgData);
                    
                    $rawFileName = 'designs/assets/' . Str::random(40) . '-raw.' . $extRaw;
                    Storage::disk('public')->put($rawFileName, $decodedRaw);
                    $rawAssetsPaths[] = $rawFileName;
                }
            }
        }

        $desain = Desain::create([
            'id_customer' => auth()->guard('customer')->id(),
            'id_template' => $request->id_template, // Bisa null
            'file_desain' => $fileName,
            'file_desain_belakang' => $fileNameBelakang,
            'file_desain_kiri' => $fileNameKiri,
            'file_desain_kanan' => $fileNameKanan,
            'canvas_front' => $request->canvas_front,
            'canvas_back' => $request->canvas_back,
            'canvas_left' => $request->canvas_left,
            'canvas_right' => $request->canvas_right,
            'harga_desain' => $request->harga_desain,
            'warna_baju' => $request->warna_baju,
            'raw_assets' => !empty($rawAssetsPaths) ? $rawAssetsPaths : null,
            'detail_sablon' => $request->detail_sablon,
        ]);

        // Simpan langsung ke keranjang belanja
        Cart::create([
            'id_customer' => auth()->guard('customer')->id(),
            'id_produk' => $request->id_produk,
            'id_desain' => $desain->id_desain,
            'quantity' => 1,
        ]);

        return response()->json([
            'success' => true, 
            'id_desain' => $desain->id_desain,
            'redirect_url' => route('customer.cart.index')
        ]);
    }

    public function update(StoreDesainRequest $request, Desain $desain)
    {
        $validated = $request->validated();

        if ($desain->id_customer !== auth()->guard('customer')->id()) {
            abort(403);
        }

        // We create a NEW design instead of modifying the old one (Versioning)
        $newDesainData = [
            'id_customer' => $desain->id_customer,
            'id_template' => $request->id_template ?? $desain->id_template,
            'parent_id' => $desain->id_desain, // Link to previous version
            'warna_baju' => $request->warna_baju,
            'harga_desain' => $request->harga_desain,
            'detail_sablon' => $request->detail_sablon,
            'file_desain' => $desain->file_desain, // Default to old if not updated
            'file_desain_belakang' => $desain->file_desain_belakang,
            'file_desain_kiri' => $desain->file_desain_kiri,
            'file_desain_kanan' => $desain->file_desain_kanan,
            'canvas_front' => $request->filled('canvas_front') ? $request->canvas_front : $desain->canvas_front,
            'canvas_back' => $request->filled('canvas_back') ? $request->canvas_back : $desain->canvas_back,
            'canvas_left' => $request->filled('canvas_left') ? $request->canvas_left : $desain->canvas_left,
            'canvas_right' => $request->filled('canvas_right') ? $request->canvas_right : $desain->canvas_right,
            'raw_assets' => $desain->raw_assets,
        ];

        // Process new front design
        if ($request->filled('file_desain')) {
            $base64_image = $request->file_desain;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                $image_data = base64_decode(str_replace(' ', '+', substr($base64_image, strpos($base64_image, ',') + 1)));
                $fileName = 'designs/' . Str::random(40) . '-v2.' . strtolower($type[1]);
                Storage::disk('public')->put($fileName, $image_data);
                $newDesainData['file_desain'] = $fileName;
            }
        }

        // Process new back design
        if ($request->filled('file_desain_belakang')) {
            $base64_image_b = $request->file_desain_belakang;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image_b, $type)) {
                $image_data = base64_decode(str_replace(' ', '+', substr($base64_image_b, strpos($base64_image_b, ',') + 1)));
                $fileName = 'designs/' . Str::random(40) . '-back-v2.' . strtolower($type[1]);
                Storage::disk('public')->put($fileName, $image_data);
                $newDesainData['file_desain_belakang'] = $fileName;
            }
        }

        // Process new left design
        if ($request->filled('file_desain_kiri')) {
            $base64_image_k = $request->file_desain_kiri;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image_k, $type)) {
                $image_data = base64_decode(str_replace(' ', '+', substr($base64_image_k, strpos($base64_image_k, ',') + 1)));
                $fileName = 'designs/' . Str::random(40) . '-left-v2.' . strtolower($type[1]);
                Storage::disk('public')->put($fileName, $image_data);
                $newDesainData['file_desain_kiri'] = $fileName;
            }
        }

        // Process new right design
        if ($request->filled('file_desain_kanan')) {
            $base64_image_kn = $request->file_desain_kanan;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image_kn, $type)) {
                $image_data = base64_decode(str_replace(' ', '+', substr($base64_image_kn, strpos($base64_image_kn, ',') + 1)));
                $fileName = 'designs/' . Str::random(40) . '-right-v2.' . strtolower($type[1]);
                Storage::disk('public')->put($fileName, $image_data);
                $newDesainData['file_desain_kanan'] = $fileName;
            }
        }

        // Process new raw assets
        if ($request->has('raw_assets') && is_array($request->raw_assets)) {
            $rawAssetsPaths = [];
            foreach ($request->raw_assets as $rawBase64) {
                if (preg_match('/^data:image\/(\w+);base64,/', $rawBase64, $type)) {
                    $image_data = base64_decode(str_replace(' ', '+', substr($rawBase64, strpos($rawBase64, ',') + 1)));
                    $fileName = 'designs/assets/' . Str::random(40) . '-raw-v2.' . strtolower($type[1]);
                    Storage::disk('public')->put($fileName, $image_data);
                    $rawAssetsPaths[] = $fileName;
                }
            }
            if (!empty($rawAssetsPaths)) {
                $newDesainData['raw_assets'] = $rawAssetsPaths;
            }
        }

        // Create new design record
        $newDesain = Desain::create($newDesainData);

        // Find OrderDetails using the OLD design that need revision, and link them to the NEW design
        $orderDetails = \App\Models\OrderDetail::where('id_desain', $desain->id_desain)
                            ->where('status_desain', 'revision_required')
                            ->get();
                            
        foreach ($orderDetails as $od) {
            $od->update([
                'id_desain' => $newDesain->id_desain,
                'status_desain' => 'pending',
                'catatan_admin' => 'Telah diperbaiki oleh pelanggan pada ' . now()->format('d M Y H:i')
            ]);
        }

        return response()->json([
            'success' => true, 
            'id_desain' => $newDesain->id_desain,
            'redirect_url' => route('customer.orders.index')
        ]);
    }
}
