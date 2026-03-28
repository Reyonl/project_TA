<?php

use Illuminate\Support\Facades\Route;

use App\Models\Produk;

Route::get('/', function () {
    // Dynamic products for landing page
    $produks = Produk::take(3)->get();
    return view('welcome', compact('produks'));
})->name('home');

use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\DesignController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;

// Public Catalog
Route::get('/katalog/{produk}', [ProductController::class, 'show'])->name('katalog.show');

// Customer Routes
Route::middleware('auth:customer')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::get('/products/{produk}', [ProductController::class, 'show'])->name('products.show');
    
    Route::get('/design/{produk}', [DesignController::class, 'index'])->name('designs.editor');
    Route::post('/design', [DesignController::class, 'store'])->name('designs.store');
    
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Pesanan Customer
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');


    // =========================================================
    // API Stiker - Sumber Ganda (Iconify + DiceBear)
    // =========================================================
    Route::get('/api/stickers', function (\Illuminate\Http\Request $request) {
        $query = trim($request->query('q', 'smile'));

        $results = [];

        // ---- SUMBER 1: Iconify.design (200k+ ikon, brand logos, dll) ----
        try {
            $iconifySearch = \Illuminate\Support\Facades\Http::timeout(6)
                ->get("https://api.iconify.design/search", [
                    'query'  => $query,
                    'limit'  => 36, // Tingkatkan variasi icon
                ]);

            if ($iconifySearch->successful()) {
                $icons = $iconifySearch->json('icons', []);
                foreach ($icons as $icon) {
                    // Format: "prefix:name" -> ex: "logos:nike", "mdi:star"
                    [$prefix, $name] = explode(':', $icon, 2);
                    $results[] = [
                        'name'   => $icon,
                        'url'    => route('customer.api.sticker.proxy', [
                            'source' => 'iconify',
                            'prefix' => $prefix,
                            'name'   => $name,
                        ]),
                        'source' => 'Iconify',
                    ];
                }
            }
        } catch (\Exception $e) {
            // Lanjut ke DiceBear
        }

        // ---- SUMBER 2: DiceBear (Avatar/Emoji unik berdasar text) ----
        // Hanya kita sertakan style yang universal seperti fun-emoji dan shapes
        $seed  = str_replace(' ', '', ucwords($query));
        $dicebearStyles = [
            'fun-emoji'  => 'Emoji',
            'shapes'     => 'Shape Art',
        ];
        foreach ($dicebearStyles as $api => $styleName) {
            $results[] = [
                'name'   => "$styleName ($query)",
                'url'    => route('customer.api.sticker.proxy', [
                    'source' => 'dicebear',
                    'style'  => $api,
                    'seed'   => $seed,
                ]),
                'source' => 'DiceBear',
            ];
        }

        return response()->json(['success' => true, 'data' => $results]);
    })->name('api.stickers');

    // =========================================================
    // Proxy endpoint universal - menangani Iconify & DiceBear
    // =========================================================
    Route::get('/api/stickers/proxy', function (\Illuminate\Http\Request $request) {
        $source = $request->query('source', 'dicebear');

        if ($source === 'iconify') {
            $prefix = preg_replace('/[^a-z0-9\-]/', '', $request->query('prefix', 'mdi'));
            $name   = preg_replace('/[^a-z0-9\-]/', '', $request->query('name', 'star'));
            
            // Iconify mendukung ukuran via parameter width/height
            $imageUrl = "https://api.iconify.design/{$prefix}/{$name}.svg?width=128&height=128";
            $contentType = 'image/svg+xml';

        } else {
            // DiceBear (PNG aman untuk Fabric.js)
            $allowedStyles = ['fun-emoji','bottts','pixel-art','miniavs','identicon','thumbs','shapes','adventurer'];
            $style = $request->query('style', 'fun-emoji');
            $seed  = preg_replace('/[^a-zA-Z0-9]/', '', $request->query('seed', 'Smile'));
            if (!in_array($style, $allowedStyles)) abort(400, 'Style tidak valid.');

            $imageUrl    = "https://api.dicebear.com/8.x/{$style}/png?seed={$seed}&size=128";
            $contentType = 'image/png';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(8)->get($imageUrl);
            if ($response->successful()) {
                return response($response->body(), 200, [
                    'Content-Type'                => $contentType,
                    'Access-Control-Allow-Origin' => '*',
                    'Cache-Control'               => 'public, max-age=86400',
                ]);
            }
        } catch (\Exception $e) { /* fallback */ }

        return redirect($imageUrl);
    })->name('api.sticker.proxy');
});

// Admin & Owner Routes
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Owner\ReportController;

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', function () {
        if(auth()->guard('admin')->user()->role == 'owner') {
            return redirect()->route('admin.report.index');
        }
        return view('admin.dashboard');
    })->name('dashboard');

    // Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::resource('templates', TemplateController::class);
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });

    // Owner Only
    Route::middleware('role:owner')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('report.index');
    });
});

require __DIR__.'/auth.php';
