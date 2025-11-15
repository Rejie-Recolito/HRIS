<?php

// Development router for PHP's built-in server.
// Serves static files from the `public` directory and falls back to Laravel's
// `public/index.php` front controller. This avoids depending on a vendor
// `server.php` that may not be present in some framework versions.

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$requested = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($requested)) {
    return false; // serve the requested resource as-is
}

require_once __DIR__ . '/public/index.php';
