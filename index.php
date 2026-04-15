<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

// Load .env if present (Railway injects env vars directly)
if (file_exists(__DIR__ . '/.env')) {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

$webhook = new AccountaBuddy\Discord\Webhook();
$webhook->handle();
