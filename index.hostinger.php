<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| index.php KHUSUS HOSTINGER
|--------------------------------------------------------------------------
| File ini digunakan saat SELURUH project Laravel ada di dalam public_html/.
| Upload file ini ke public_html/ dan rename menjadi index.php
|
| Struktur yang benar di public_html/:
|   public_html/
|   ├── index.php         <-- file ini (rename dari index.hostinger.php)
|   ├── .htaccess         <-- copy dari public/.htaccess
|   ├── build/            <-- copy dari public/build/
|   ├── images/           <-- copy dari public/images/
|   ├── favicon.ico       <-- copy dari public/favicon.ico
|   ├── robots.txt        <-- copy dari public/robots.txt
|   ├── app/
|   ├── bootstrap/
|   ├── config/
|   ├── database/
|   ├── resources/
|   ├── routes/
|   ├── storage/
|   ├── vendor/
|   └── .env
|--------------------------------------------------------------------------
*/

// Determine if the application is in maintenance mode...
// Path: __DIR__ karena storage sekarang satu level dengan index.php
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
// Path: __DIR__ bukan __DIR__.'/../' karena vendor satu level dengan index.php
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
