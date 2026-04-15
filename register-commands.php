<?php

declare(strict_types=1);

/**
 * Run this once (or on each deploy) to register slash commands with Discord.
 * Usage: php register-commands.php
 * Optional: php register-commands.php <guild_id>  (registers to a specific guild for faster testing)
 */

require_once __DIR__ . '/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

use AccountaBuddy\Config;
use AccountaBuddy\Discord\Api;

$guildId  = $argv[1] ?? null;
$appId    = Config::appId();

$commands = [
    [
        'name'        => 'goal-ABuddy',
        'description' => 'Manage your AccountaBuddy goals',
        'options'     => [
            [
                'type'        => 1, // SUB_COMMAND
                'name'        => 'new',
                'description' => 'Create a new accountability goal',
            ],
            [
                'type'        => 1,
                'name'        => 'list',
                'description' => 'List all your active goals',
            ],
            [
                'type'        => 1,
                'name'        => 'view',
                'description' => 'View details for a specific goal',
                'options'     => [[
                    'type'        => 3, // STRING
                    'name'        => 'goal',
                    'description' => 'Goal name or ID',
                    'required'    => true,
                ]],
            ],
            [
                'type'        => 1,
                'name'        => 'pause',
                'description' => 'Pause a goal',
                'options'     => [[
                    'type'        => 3,
                    'name'        => 'goal',
                    'description' => 'Goal name or ID',
                    'required'    => true,
                ]],
            ],
            [
                'type'        => 1,
                'name'        => 'cancel',
                'description' => 'Cancel a goal',
                'options'     => [[
                    'type'        => 3,
                    'name'        => 'goal',
                    'description' => 'Goal name or ID',
                    'required'    => true,
                ]],
            ],
            [
                'type'        => 1,
                'name'        => 'appeal',
                'description' => 'Appeal a broken streak (community vote)',
                'options'     => [[
                    'type'        => 3,
                    'name'        => 'goal',
                    'description' => 'Goal name or ID',
                    'required'    => true,
                ]],
            ],
        ],
    ],
    [
        'name'        => 'accountabuddy',
        'description' => 'AccountaBuddy server management',
        'options'     => [
            [
                'type'                     => 1,
                'name'                     => 'setup',
                'description'              => 'Configure the accountability channel and timezone (admin only)',
                'default_member_permissions' => '32', // MANAGE_GUILD
                'options'                  => [
                    [
                        'type'        => 7, // CHANNEL
                        'name'        => 'channel',
                        'description' => 'The channel where check-ins are posted',
                        'required'    => true,
                    ],
                    [
                        'type'        => 3,
                        'name'        => 'timezone',
                        'description' => 'Server timezone (TZ database name, e.g. America/New_York)',
                        'required'    => false,
                    ],
                ],
            ],
            [
                'type'        => 1,
                'name'        => 'leaderboard',
                'description' => 'Show current badge standings',
            ],
        ],
    ],
];

// Register globally or to a specific guild
if ($guildId) {
    $path = "/applications/{$appId}/guilds/{$guildId}/commands";
    echo "Registering commands to guild {$guildId}...\n";
} else {
    $path = "/applications/{$appId}/commands";
    echo "Registering global commands (may take up to 1 hour to propagate)...\n";
}

try {
    // PUT replaces all commands at once
    $url = 'https://discord.com/api/v10' . $path;
    $json = json_encode($commands);

    $ctx = stream_context_create([
        'http' => [
            'method'        => 'PUT',
            'header'        => implode("\r\n", [
                'Content-Type: application/json',
                'Authorization: Bot ' . Config::botToken(),
                'User-Agent: AccountaBuddy/1.0',
            ]),
            'content'       => $json,
            'ignore_errors' => true,
        ],
    ]);

    $result = file_get_contents($url, false, $ctx);
    $status = (int)(explode(' ', $http_response_header[0])[1] ?? 0);

    if ($status >= 200 && $status < 300) {
        $registered = json_decode($result, true);
        echo "✅ Registered " . count($registered) . " command(s):\n";
        foreach ($registered as $cmd) {
            echo "  - /{$cmd['name']} (id: {$cmd['id']})\n";
        }
    } else {
        echo "❌ Failed ({$status}):\n{$result}\n";
        exit(1);
    }
} catch (\Throwable $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    exit(1);
}
