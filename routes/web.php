<?php

use Illuminate\Support\Facades\Route;

use App\Models\Produk;

Route::get('/', function () {
    // Dynamic products for landing page
    $produks = Produk::where('jenis_produk', '!=', 'topi')->take(3)->get();
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
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
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

require __DIR__.'/auth.php';
