<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Desain;
use App\Models\Template;
use App\Models\Produk;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    public function index(Produk $produk)
    {
        $templates = Template::all();
        return view('customer.designs.editor', compact('templates', 'produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_desain' => 'required|string',
            'lebar_cm' => 'required|numeric',
            'tinggi_cm' => 'required|numeric',
            'file_desain_belakang' => 'nullable|string',
            'lebar_cm_belakang' => 'nullable|numeric',
            'tinggi_cm_belakang' => 'nullable|numeric',
            'warna_baju' => 'nullable|string',
        ]);

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

        $desain = Desain::create([
            'id_customer' => auth()->guard('customer')->id(),
            'id_template' => $request->id_template, // Bisa null
            'file_desain' => $fileName,
            'file_desain_belakang' => $fileNameBelakang,
            'lebar_cm' => $request->lebar_cm,
            'lebar_cm_belakang' => $request->lebar_cm_belakang,
            'tinggi_cm' => $request->tinggi_cm,
            'tinggi_cm_belakang' => $request->tinggi_cm_belakang,
            'harga_desain' => 20000, // Misal tarif sablon custom 20.000
            'tanggal_upload' => now(),
            'warna_baju' => $request->warna_baju,
        ]);

        return response()->json(['success' => true, 'id_desain' => $desain->id_desain]);
    }
}
