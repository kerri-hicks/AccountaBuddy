<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Types;

class Leaderboard
{
    public static function handle(array $interaction): array
    {
        $guildId = $interaction['guild_id'] ?? '';

        // Current weekly period (Monday-based)
        $monday    = date('Y-m-d', strtotime('monday this week'));
        $sunday    = date('Y-m-d', strtotime('sunday this week'));

        $badges = Database::fetchAll(
            "SELECT b.badge_type, b.user_id, gm.display_name
               FROM badges b
               JOIN guild_members gm ON gm.user_id = b.user_id AND gm.guild_id = b.guild_id
              WHERE b.guild_id = :gid AND b.period_type = 'weekly'
                AND b.period_start = :start AND b.period_end = :end
              ORDER BY b.badge_type",
            [':gid' => $guildId, ':start' => $monday, ':end' => $sunday]
        );

        if (empty($badges)) {
            // Show current standings from interactions
            return self::currentStandings($guildId, $monday, $sunday);
        }

        $lines = ["**🏆 Weekly Leaderboard ({$monday} – {$sunday})**"];
        foreach ($badges as $b) {
            $icon = self::badgeIcon($b['badge_type']);
            $lines[] = "{$icon} **" . self::badgeLabel($b['badge_type']) . "**: {$b['display_name']}";
        }

        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => implode("\n", $lines), 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }

    private static function currentStandings(string $guildId, string $from, string $to): array
    {
        // Iron streak: top active streaks
        $streaks = Database::fetchAll(
            "SELECT g.streak_count, gm.display_name
               FROM goals g
               JOIN guild_members gm ON gm.user_id = g.user_id AND gm.guild_id = g.guild_id
              WHERE g.guild_id = :gid AND g.status = 'active' AND g.streak_count > 0
              ORDER BY g.streak_count DESC LIMIT 5",
            [':gid' => $guildId]
        );

        $lines = ["**🏆 Current Standings (week of {$from})**"];

        if ($streaks) {
            $lines[] = "\n💪 **Top Streaks:**";
            foreach ($streaks as $s) {
                $lines[] = "• {$s['display_name']} — {$s['streak_count']} cycles";
            }
        }

        $lines[] = "\n_Full badge awards are calculated at the end of each period._";

        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => implode("\n", $lines), 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }

    private static function badgeIcon(string $type): string
    {
        return match ($type) {
            Types::BADGE_ENCOURAGING  => '🏆',
            Types::BADGE_RELENTLESS   => '🔥',
            Types::BADGE_GHOST        => '👻',
            Types::BADGE_IRON_STREAK  => '💪',
            Types::BADGE_COMEBACK_KID => '🦊',
            Types::BADGE_OVERACHIEVER => '🎯',
            default                   => '🏅',
        };
    }

    private static function badgeLabel(string $type): string
    {
        return match ($type) {
            Types::BADGE_ENCOURAGING  => 'Most Encouraging',
            Types::BADGE_RELENTLESS   => 'Most Relentless',
            Types::BADGE_GHOST        => 'The Ghost',
            Types::BADGE_IRON_STREAK  => 'Iron Streak',
            Types::BADGE_COMEBACK_KID => 'Comeback Kid',
            Types::BADGE_OVERACHIEVER => 'Overachiever',
            default                   => $type,
        };
    }
}
