<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

// Load .env if present
if (file_exists(__DIR__ . '/../.env')) {
    foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

use AccountaBuddy\Cron\CheckInScheduler;
use AccountaBuddy\Cron\EscalationRunner;
use AccountaBuddy\Cron\CycleEvaluator;
use AccountaBuddy\Cron\AppealExpiry;
use AccountaBuddy\Cron\BadgeAwarder;

echo "[" . gmdate('Y-m-d H:i:s') . " UTC] AccountaBuddy cron starting\n";

$jobs = [
    'CheckInScheduler' => new CheckInScheduler(),
    'EscalationRunner' => new EscalationRunner(),
    'CycleEvaluator'   => new CycleEvaluator(),
    'AppealExpiry'     => new AppealExpiry(),
    'BadgeAwarder'     => new BadgeAwarder(),
];

foreach ($jobs as $name => $job) {
    try {
        echo "[" . gmdate('H:i:s') . "] Running {$name}...\n";
        $job->run();
        echo "[" . gmdate('H:i:s') . "] {$name} done.\n";
    } catch (\Throwable $e) {
        echo "[" . gmdate('H:i:s') . "] ERROR in {$name}: {$e->getMessage()}\n";
        error_log("Cron {$name} fatal: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    }
}

echo "[" . gmdate('Y-m-d H:i:s') . " UTC] Cron complete.\n";
