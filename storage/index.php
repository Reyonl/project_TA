<?php
/**
 * Mini file server untuk folder storage di shared hosting.
 * Dipanggil via .htaccess saat ada request ke /storage/...
 */

// Ambil path yang diminta, buang prefix /storage/
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = preg_replace('#^/storage/#', '', $requestUri);

// Lokasi asli file di storage/app/public/
$fullPath = __DIR__ . '/app/public/' . $requestUri;
$fullPath = realpath($fullPath);

// Keamanan: pastikan file ada di dalam storage/app/public/ saja
$allowedBase = realpath(__DIR__ . '/app/public');
if (!$fullPath || !$allowedBase || strpos($fullPath, $allowedBase) !== 0) {
    http_response_code(404);
    exit('Not found');
}

if (!is_file($fullPath)) {
    http_response_code(404);
    exit('Not found');
}

// Deteksi MIME type dan serve file
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($fullPath);
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: public, max-age=31536000');
readfile($fullPath);
exit;
