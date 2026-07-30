<?php

use Illuminate\Support\Facades\Route;

use App\Models\Produk;

Route::get('/', function () {
    // Group products by their base name to avoid cluttering landing page with variants
    $allProduks = Produk::where('jenis_produk', '!=', 'topi')->get();
    $grouped = $allProduks->groupBy(function($item) {
        $parts = explode(' ', $item->nama_produk);
        return ($parts[0] === 'Kaos') ? $parts[0] . ' ' . ($parts[1] ?? '') : $parts[0];
    });
    $produks = $grouped->map->first()->take(3)->values();
    
    return view('welcome', compact('produks'));
})->name('home');

use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\DesignController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\CartController;

// Public Catalog
Route::get('/katalog/{produk}', [ProductController::class, 'show'])->name('katalog.show');

// Customer Routes
Route::middleware('auth:customer')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::get('/products/{produk}', [ProductController::class, 'show'])->name('products.show');
    
    Route::get('/design/{produk}', [DesignController::class, 'index'])->name('designs.editor');
    Route::post('/design', [DesignController::class, 'store'])->name('designs.store');
    Route::patch('/design/{desain}', [DesignController::class, 'update'])->name('designs.update');
    
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Keranjang Belanja
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/direct/{produk}', [CartController::class, 'storeDirect'])->name('cart.storeDirect');
    Route::patch('/cart/{cart}/quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

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
                        // URL langsung ke CDN Iconify (mendukung CORS)
                        'url'    => "https://api.iconify.design/{$prefix}/{$name}.svg",
                        'source' => 'Iconify',
                    ];
                }
            }
        } catch (\Exception $e) {
            // Lanjut ke DiceBear
        }

        // ---- SUMBER 2: DiceBear (Avatar/Emoji unik berdasar text) ----
        // Hanya kita sertakan style yang universal seperti fun-emoji dan shapes
        $seed  = preg_replace('/[^a-zA-Z0-9]/', '', ucwords($query));
        $dicebearStyles = [
            'fun-emoji'  => 'Emoji',
            'shapes'     => 'Shape Art',
        ];
        foreach ($dicebearStyles as $api => $styleName) {
            $results[] = [
                'name'   => "$styleName ($query)",
                // URL langsung ke CDN DiceBear (mendukung CORS)
                'url'    => "https://api.dicebear.com/8.x/{$api}/png?seed={$seed}&size=128",
                'source' => 'DiceBear',
            ];
        }

        return response()->json(['success' => true, 'data' => $results]);
    })->name('api.stickers');
});

// Admin & Owner Routes
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Owner\ReportController;

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::resource('products', AdminProductController::class);
        Route::resource('templates', TemplateController::class);
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::patch('orders/{order}/desain/{orderDetail}', [AdminOrderController::class, 'updateStatusDesain'])->name('orders.updateStatusDesain');
    });

    // Owner & Admin
    Route::middleware('role:owner,admin')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('report.index');
    });
});

// ==================== DEBUG SEMENTARA ====================
Route::get('/debug-storage', function () {
    $storagePath  = storage_path('app/public');
    $publicPath   = public_path();
    $basePath     = base_path();

    // Scan isi folder storage/app/public jika ada
    $files = [];
    if (is_dir($storagePath)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storagePath, FilesystemIterator::SKIP_DOTS)) as $file) {
            $files[] = $file->getPathname();
            if (count($files) >= 20) { $files[] = '... (terpotong)'; break; }
        }
    }

    return response()->json([
        'base_path'      => $basePath,
        'public_path'    => $publicPath,
        'storage_path'   => $storagePath,
        'storage_exists' => is_dir($storagePath),
        'files_found'    => $files,
    ]);
});
// ==================== END DEBUG ====================

// Fallback Route untuk melayani file storage langsung melalui PHP
// Sangat berguna di shared hosting di mana symlink bermasalah
Route::get('storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    // DEBUG SEMENTARA - hapus setelah solved
    if (!file_exists($fullPath)) {
        return response()->json([
            'error'     => 'File not found',
            'path_requested' => $path,
            'full_path_checked' => $fullPath,
            'storage_dir_exists' => is_dir(storage_path('app/public')),
            'designs_dir_exists' => is_dir(storage_path('app/public/designs')),
            'designs_dir_contents' => is_dir(storage_path('app/public/designs'))
                ? array_slice(scandir(storage_path('app/public/designs')), 0, 10)
                : [],
        ]);
    }
    
    $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);
    return response()->file($fullPath, [
        'Content-Type' => $mimeType
    ]);
})->where('path', '.*');



require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
