<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Types;

class GoalView
{
    public static function handle(array $interaction): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $subcommandOptions = [];
        foreach ($interaction['data']['options'] ?? [] as $opt) {
            if (($opt['type'] ?? 0) === 1 && ($opt['name'] ?? '') === 'view') {
                $subcommandOptions = $opt['options'] ?? [];
                break;
            }
        }
        $options = !empty($subcommandOptions) ? $subcommandOptions : ($interaction['data']['options'] ?? []);
        $goalArg = '';
        foreach ($options as $opt) {
            if ($opt['name'] === 'goal') {
                $goalArg = (string)$opt['value'];
                break;
            }
        }

        $goal = self::findGoal($userId, $guildId, $goalArg);

        if (!$goal) {
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => [
                    'content' => "Goal not found. Use `/goal-abuddy list` to see your goals.",
                    'flags'   => Types::FLAG_EPHEMERAL,
                ],
            ];
        }

        // Fetch current cycle
        $cycle = Database::fetch(
            "SELECT * FROM cycles WHERE goal_id = :gid AND status = 'active' ORDER BY start_date DESC LIMIT 1",
            [':gid' => $goal['id']]
        );

        $personalityLabel = match ($goal['personality']) {
            Types::PERSONALITY_HYPE      => '🔥📣 Hype Coach',
            Types::PERSONALITY_DRY       => '📈📊 Dry Colleague',
            Types::PERSONALITY_SARCASTIC => '👀🦊 Sarcastic Friend',
            Types::PERSONALITY_HARSH     => '🗿💀 Harsh Critic',
            default                      => $goal['personality'],
        };

        $cadence = GoalList::formatCadence($goal['cadence_type'], (int)$goal['cadence_target']);

        $userRow = Database::fetch("SELECT timezone, timezone_set FROM users WHERE id = :id", [':id' => $userId]);
        $timezoneSet = false;
        if ($userRow && ($userRow['timezone_set'] ?? false)) {
            $timezone = $userRow['timezone'];
            $timezoneSet = true;
        } else {
            $config = Database::fetch("SELECT timezone FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
            $timezone = $config['timezone'] ?? 'UTC';
        }

        $localCheckinTime = $goal['checkin_time'];
        $utcFormatted = substr($goal['checkin_time'], 0, 5);
        $parsedOk = false;
        try {
            $utcTime = new \DateTime($goal['checkin_time'], new \DateTimeZone('UTC'));
            $utcTime->setTimezone(new \DateTimeZone($timezone));
            if ($timezoneSet) {
                $localCheckinTime = $utcTime->format('H:i');
            } else {
                $localCheckinTime = $utcTime->format('H:i') . ' ' . $timezone;
            }
            $parsedOk = true;
        } catch (\Throwable $e) {
            $localCheckinTime = $utcFormatted . ' UTC';
        }

        $checkinLine = ($timezoneSet && $parsedOk)
            ? "Cadence: {$cadence} | Check-in: {$localCheckinTime}"
            : "Cadence: {$cadence} | Check-in: {$localCheckinTime} ({$utcFormatted} UTC)";

        $lines = [
            "**{$goal['name']}**",
            "Status: {$goal['status']} | Personality: {$personalityLabel}",
            $checkinLine,
        ];

        if ($goal['description']) {
            $lines[] = "_{$goal['description']}_";
        }

        if ($goal['cadence_type'] !== Types::CADENCE_ONE_TIME) {
            $lines[] = "Streak: {$goal['streak_count']} (best: {$goal['streak_best']})";
        }

        if ($cycle) {
            $lines[] = "Current cycle: {$cycle['start_date']} → {$cycle['end_date']} | {$cycle['completions']}/{$cycle['target']} completions";
        }

        if ($goal['cadence_type'] === Types::CADENCE_ONE_TIME) {
            $lines[] = "Reminder count: {$goal['reminder_count']}";
        }

        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => [
                'content' => implode("\n", $lines),
                'flags'   => Types::FLAG_EPHEMERAL,
            ],
        ];
    }

    public static function findGoal(string $userId, string $guildId, string $arg): ?array
    {
        // Try by ID first
        if (is_numeric($arg)) {
            $goal = Database::fetch(
                "SELECT * FROM goals WHERE id = :id AND user_id = :uid AND guild_id = :gid",
                [':id' => $arg, ':uid' => $userId, ':gid' => $guildId]
            );
            if ($goal) return $goal;
        }

        // Try by name (partial match)
        return Database::fetch(
            "SELECT * FROM goals WHERE user_id = :uid AND guild_id = :gid AND LOWER(name) LIKE :name AND status NOT IN ('cancelled','completed') ORDER BY created_at ASC LIMIT 1",
            [':uid' => $userId, ':gid' => $guildId, ':name' => '%' . strtolower($arg) . '%']
        );
    }
}
