<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Deteksi Public Path secara Otomatis
|--------------------------------------------------------------------------
| Di Hostinger: index.php ada di public_html/ (satu level dengan vendor/)
|   → publicPath = basePath (public_html/)
|
| Di lokal (Laragon): index.php ada di public/ (satu level DI BAWAH vendor/)
|   → publicPath = basePath/public (default Laravel)
|--------------------------------------------------------------------------
*/
$basePath = dirname(__DIR__);
$publicPath = file_exists($basePath.'/vendor/autoload.php')
    // index.php satu level dengan vendor/ → Hostinger mode
    ? $basePath
    // index.php berada di dalam public/ → local/default mode
    : $basePath.'/public';

return Application::configure(basePath: $basePath)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create()
    ->usePublicPath($publicPath);

