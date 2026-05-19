<?php

declare(strict_types=1);

// Never let PHP output raw errors into the HTTP response — it corrupts JSON
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');

require_once __DIR__ . '/autoload.php';

// Load .env if present (Railway injects env vars directly)
if (file_exists(__DIR__ . '/.env')) {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

try {
    $webhook = new AccountaBuddy\Discord\Webhook();
    $webhook->handle();
} catch (\Throwable $e) {
    error_log('Unhandled exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode([
        'type' => 4,
        'data' => ['content' => 'An internal error occurred. Please try again.', 'flags' => 64],
    ]);
}
