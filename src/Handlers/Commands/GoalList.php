<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Types;

class GoalList
{
    public static function handle(array $interaction): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $goals = Database::fetchAll(
            "SELECT id, name, cadence_type, cadence_target, status, streak_count, checkin_time
               FROM goals
              WHERE user_id = :uid AND guild_id = :gid AND status IN ('active','paused','on_hold')
              ORDER BY created_at ASC",
            [':uid' => $userId, ':gid' => $guildId]
        );

        if (empty($goals)) {
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => [
                    'content' => "You have no active goals. Use `/goal-ABuddy new` to create one.",
                    'flags'   => Types::FLAG_EPHEMERAL,
                ],
            ];
        }

        $lines = [];
        foreach ($goals as $g) {
            $status = match ($g['status']) {
                'paused'  => ' *(paused)*',
                'on_hold' => ' *(on hold)*',
                default   => '',
            };
            $streak = $g['streak_count'] > 0 ? " — streak: {$g['streak_count']}" : '';
            $cadence = self::formatCadence($g['cadence_type'], (int)$g['cadence_target']);
            $lines[] = "**#{$g['id']}** {$g['name']}{$status} — {$cadence}{$streak} — check-in: {$g['checkin_time']} UTC";
        }

        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => [
                'content' => "**Your active goals:**\n" . implode("\n", $lines),
                'flags'   => Types::FLAG_EPHEMERAL,
            ],
        ];
    }

    public static function formatCadence(string $type, int $target): string
    {
        return match ($type) {
            Types::CADENCE_ONE_TIME      => 'one-time',
            Types::CADENCE_DAILY         => 'daily',
            Types::CADENCE_WEEKLY_ONCE   => 'once a week',
            Types::CADENCE_WEEKLY_X      => "{$target}×/week",
            Types::CADENCE_MONTHLY_ONCE  => 'once per 30 days',
            Types::CADENCE_MONTHLY_X     => "{$target}×/30 days",
            default                      => $type,
        };
    }
}
