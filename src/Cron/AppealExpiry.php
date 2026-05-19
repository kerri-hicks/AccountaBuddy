<?php

declare(strict_types=1);

namespace AccountaBuddy\Cron;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class AppealExpiry
{
    public function run(): void
    {
        $now = gmdate('Y-m-d H:i:s');

        $appeals = Database::fetchAll(
            "SELECT sa.*, g.personality, g.name AS goal_name, g.id AS goal_id,
                    g.streak_best, g.guild_id,
                    gm.display_name, sc.accountability_channel_id
               FROM streak_appeals sa
               JOIN goals g ON g.id = sa.goal_id
               JOIN guild_members gm ON gm.guild_id = g.guild_id AND gm.user_id = sa.user_id
               JOIN server_config sc ON sc.guild_id = g.guild_id
              WHERE sa.status = 'open'
                AND sa.expires_at <= :now",
            [':now' => $now]
        );

        foreach ($appeals as $appeal) {
            try {
                $this->resolveAppeal($appeal);
            } catch (\Throwable $e) {
                error_log("AppealExpiry error for appeal {$appeal['id']}: " . $e->getMessage());
            }
        }
    }

    private function resolveAppeal(array $appeal): void
    {
        $voteCount = (int)(Database::fetch(
            "SELECT COUNT(*) AS cnt FROM appeal_votes WHERE appeal_id = :id",
            [':id' => $appeal['id']]
        )['cnt'] ?? 0);

        $channelId = $appeal['accountability_channel_id'];
        $vars      = ['name' => $appeal['display_name'], 'goal' => $appeal['goal_name']];

        if ($voteCount >= 5) {
            // Approved
            Database::execute(
                "UPDATE streak_appeals SET status = 'approved' WHERE id = :id",
                [':id' => $appeal['id']]
            );
            Database::execute(
                "UPDATE goals SET streak_count = streak_best WHERE id = :id",
                [':id' => $appeal['goal_id']]
            );

            $msg = Library::get($appeal['personality'], 'comeback', $vars);
            Api::sendMessage($channelId, [
                'content' => "✅ **Streak appeal approved** for {$appeal['display_name']} on **{$appeal['goal_name']}**! "
                           . "({$voteCount} votes) Streak reinstated.\n{$msg}",
            ]);
        } else {
            // Denied
            Database::execute(
                "UPDATE streak_appeals SET status = 'denied' WHERE id = :id",
                [':id' => $appeal['id']]
            );

            Api::sendMessage($channelId, [
                'content' => "❌ **Streak appeal denied** for {$appeal['display_name']} on **{$appeal['goal_name']}**. "
                           . "Only {$voteCount}/5 votes in 24 hours. Broken streak stands.",
            ]);
        }
    }
}
