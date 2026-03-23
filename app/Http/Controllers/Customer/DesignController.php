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
            'warna_baju' => 'nullable|string',
        ]);

        $base64_image = $request->file_desain;
        
        // Memisahkan data MIME dan ekstensi dari data base64 sebenarnya
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
            $image_base64 = substr($base64_image, strpos($base64_image, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, jpeg, gif
            
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                return response()->json(['success' => false, 'message' => 'Invalid image type']);
            }
            
            $image_base64 = str_replace(' ', '+', $image_base64);
            $image_data = base64_decode($image_base64);
            
            if ($image_data === false) {
                return response()->json(['success' => false, 'message' => 'Base64 decode failed']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Did not match data URI with image data']);
        }

        $fileName = 'designs/' . Str::random(40) . '.' . $type;
        Storage::disk('public')->put($fileName, $image_data);

        $desain = Desain::create([
            'id_customer' => auth()->guard('customer')->id(),
            'id_template' => $request->id_template, // Bisa null
            'file_desain' => $fileName,
            'lebar_cm' => $request->lebar_cm,
            'tinggi_cm' => $request->tinggi_cm,
            'harga_desain' => 20000, // Misal tarif sablon custom 20.000
            'tanggal_upload' => now(),
            'warna_baju' => $request->warna_baju,
        ]);

        return response()->json(['success' => true, 'id_desain' => $desain->id_desain]);
    }
}
