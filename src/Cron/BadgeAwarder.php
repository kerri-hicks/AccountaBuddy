<?php

declare(strict_types=1);

namespace AccountaBuddy\Cron;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;

class BadgeAwarder
{
    public function run(): void
    {
        $today = gmdate('Y-m-d');

        // Check which periods ended yesterday and award badges for each guild
        $guilds = Database::fetchAll("SELECT DISTINCT guild_id FROM server_config");

        foreach ($guilds as $guild) {
            $guildId = $guild['guild_id'];
            try {
                $this->maybeAwardBadges($guildId, $today);
            } catch (\Throwable $e) {
                error_log("BadgeAwarder error for guild {$guildId}: " . $e->getMessage());
            }
        }
    }

    private function maybeAwardBadges(string $guildId, string $today): void
    {
        // Calculate period boundaries
        $periods = $this->getEndingPeriods($today);

        foreach ($periods as [$periodType, $periodStart, $periodEnd]) {
            // Avoid double-awarding
            $existing = Database::fetch(
                "SELECT id FROM badges WHERE guild_id = :gid AND period_type = :pt AND period_start = :ps LIMIT 1",
                [':gid' => $guildId, ':pt' => $periodType, ':ps' => $periodStart]
            );
            if ($existing) continue;

            $this->awardPeriodBadges($guildId, $periodType, $periodStart, $periodEnd);
        }
    }

    private function getEndingPeriods(string $today): array
    {
        $periods = [];
        $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));

        // Weekly: Monday–Sunday
        $dayOfWeek = (int)date('N', strtotime($today)); // 1=Mon, 7=Sun
        if ($dayOfWeek === 1) { // Monday = new week started, award last week
            $lastSunday  = date('Y-m-d', strtotime($today . ' -1 day'));
            $lastMonday  = date('Y-m-d', strtotime($today . ' -7 days'));
            $periods[] = [Types::PERIOD_WEEKLY, $lastMonday, $lastSunday];
        }

        // Monthly: every 30 days — check if yesterday was a 30-day boundary
        // (tracked per server by checking the oldest active goal's created_at)
        // Simplified: award on 1st of each calendar month
        if (date('j', strtotime($today)) === '1') {
            $firstOfLastMonth = date('Y-m-01', strtotime($today . ' -1 month'));
            $lastOfLastMonth  = date('Y-m-t', strtotime($today . ' -1 month'));
            $periods[] = [Types::PERIOD_MONTHLY, $firstOfLastMonth, $lastOfLastMonth];
        }

        // Quarterly: Jan/Apr/Jul/Oct 1
        $month = (int)date('n', strtotime($today));
        $day   = (int)date('j', strtotime($today));
        if ($day === 1 && in_array($month, [1, 4, 7, 10], true)) {
            $qStart = date('Y-m-d', strtotime($today . ' -3 months'));
            $qEnd   = $yesterday;
            $periods[] = [Types::PERIOD_QUARTERLY, $qStart, $qEnd];
        }

        // Annual: Jan 1
        if ($month === 1 && $day === 1) {
            $aStart = date('Y-m-d', strtotime($today . ' -1 year'));
            $aEnd   = $yesterday;
            $periods[] = [Types::PERIOD_ANNUAL, $aStart, $aEnd];
        }

        return $periods;
    }

    private function awardPeriodBadges(string $guildId, string $periodType, string $periodStart, string $periodEnd): void
    {
        $config = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        $channelId = $config['accountability_channel_id'] ?? null;

        $winners = [];

        // 🏆 Most Encouraging: most comments on others' goal posts
        $enc = Database::fetch(
            "SELECT i.actor_user_id, gm.display_name, COUNT(*) AS cnt
               FROM interactions i
               JOIN goals g ON g.id = i.target_goal_id
               JOIN guild_members gm ON gm.guild_id = :gid AND gm.user_id = i.actor_user_id
              WHERE g.guild_id = :gid
                AND i.created_at BETWEEN :start AND :end
                AND i.actor_user_id != g.user_id
              GROUP BY i.actor_user_id, gm.display_name
              ORDER BY cnt DESC LIMIT 1",
            [':gid' => $guildId, ':start' => $periodStart . ' 00:00:00', ':end' => $periodEnd . ' 23:59:59']
        );
        if ($enc) $winners[Types::BADGE_ENCOURAGING] = $enc;

        // 💪 Iron Streak: longest active streak
        $iron = Database::fetch(
            "SELECT g.user_id, gm.display_name, g.streak_count AS cnt
               FROM goals g
               JOIN guild_members gm ON gm.guild_id = :gid AND gm.user_id = g.user_id
              WHERE g.guild_id = :gid AND g.streak_count > 0
              ORDER BY g.streak_count DESC LIMIT 1",
            [':gid' => $guildId]
        );
        if ($iron) $winners[Types::BADGE_IRON_STREAK] = $iron;

        // 👻 The Ghost: most missed check-ins without engaging
        $ghost = Database::fetch(
            "SELECT c.goal_id, g.user_id, gm.display_name, COUNT(*) AS cnt
               FROM checkins c
               JOIN goals g ON g.id = c.goal_id
               JOIN guild_members gm ON gm.guild_id = :gid AND gm.user_id = g.user_id
              WHERE g.guild_id = :gid
                AND c.status = 'missed'
                AND c.scheduled_at BETWEEN :start AND :end
              GROUP BY g.user_id, gm.display_name
              ORDER BY cnt DESC LIMIT 1",
            [':gid' => $guildId, ':start' => $periodStart . ' 00:00:00', ':end' => $periodEnd . ' 23:59:59']
        );
        if ($ghost) $winners[Types::BADGE_GHOST] = $ghost;

        // 🎯 Overachiever: most early cycle completions (cycles where completions > target)
        $over = Database::fetch(
            "SELECT g.user_id, gm.display_name, COUNT(*) AS cnt
               FROM cycles cy
               JOIN goals g ON g.id = cy.goal_id
               JOIN guild_members gm ON gm.guild_id = :gid AND gm.user_id = g.user_id
              WHERE g.guild_id = :gid
                AND cy.status = 'completed'
                AND cy.completions > cy.target
                AND cy.end_date BETWEEN :start AND :end
              GROUP BY g.user_id, gm.display_name
              ORDER BY cnt DESC LIMIT 1",
            [':gid' => $guildId, ':start' => $periodStart, ':end' => $periodEnd]
        );
        if ($over) $winners[Types::BADGE_OVERACHIEVER] = $over;

        // 🦊 Comeback Kid: most streaks rebuilt after a break
        $comeback = Database::fetch(
            "SELECT c.goal_id, g.user_id, gm.display_name, COUNT(*) AS cnt
               FROM checkins c
               JOIN goals g ON g.id = c.goal_id
               JOIN guild_members gm ON gm.guild_id = :gid AND gm.user_id = g.user_id
              WHERE g.guild_id = :gid
                AND c.status = 'complete'
                AND c.responded_at BETWEEN :start AND :end
              GROUP BY g.user_id, gm.display_name
              ORDER BY cnt DESC LIMIT 1",
            [':gid' => $guildId, ':start' => $periodStart . ' 00:00:00', ':end' => $periodEnd . ' 23:59:59']
        );
        // (Simplified: using most completions after gaps as proxy for comeback)
        if ($comeback) $winners[Types::BADGE_COMEBACK_KID] = $comeback;

        if (empty($winners)) return;

        // Insert badges and post
        $announcements = [];
        $badgeIcons = [
            Types::BADGE_ENCOURAGING  => '🏆',
            Types::BADGE_RELENTLESS   => '🔥',
            Types::BADGE_GHOST        => '👻',
            Types::BADGE_IRON_STREAK  => '💪',
            Types::BADGE_COMEBACK_KID => '🦊',
            Types::BADGE_OVERACHIEVER => '🎯',
        ];
        $badgeLabels = [
            Types::BADGE_ENCOURAGING  => 'Most Encouraging',
            Types::BADGE_RELENTLESS   => 'Most Relentless',
            Types::BADGE_GHOST        => 'The Ghost',
            Types::BADGE_IRON_STREAK  => 'Iron Streak',
            Types::BADGE_COMEBACK_KID => 'Comeback Kid',
            Types::BADGE_OVERACHIEVER => 'Overachiever',
        ];

        foreach ($winners as $badgeType => $winner) {
            Database::insert('badges', [
                'user_id'     => $winner['user_id'],
                'guild_id'    => $guildId,
                'badge_type'  => $badgeType,
                'period_type' => $periodType,
                'period_start'=> $periodStart,
                'period_end'  => $periodEnd,
            ]);

            $icon  = $badgeIcons[$badgeType] ?? '🏅';
            $label = $badgeLabels[$badgeType] ?? $badgeType;
            $announcements[] = "{$icon} **{$label}**: {$winner['display_name']}";
        }

        if ($channelId && $announcements) {
            $periodLabel = ucfirst($periodType);
            $msg = "🏆 **{$periodLabel} Badge Awards ({$periodStart} – {$periodEnd})**\n"
                 . implode("\n", $announcements);
            Api::sendMessage($channelId, ['content' => $msg]);
        }
    }
}
