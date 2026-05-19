<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class GoalPause
{
    public static function handle(array $interaction): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $options = $interaction['data']['options'][0]['options'] ?? [];
        $goalArg = '';
        foreach ($options as $opt) {
            if ($opt['name'] === 'goal') { $goalArg = (string)$opt['value']; break; }
        }

        $goal = GoalView::findGoal($userId, $guildId, $goalArg);

        if (!$goal) {
            return self::ephemeral("Goal not found. Use `/goal-ABuddy list` to see your goals.");
        }

        if ($goal['cadence_type'] === Types::CADENCE_ONE_TIME) {
            return self::ephemeral("One-time goals can't be paused — they just keep reminding you until done or cancelled.");
        }

        if ($goal['status'] !== Types::GOAL_ACTIVE) {
            return self::ephemeral("This goal is already {$goal['status']}.");
        }

        // Pausing mid-cycle breaks the streak
        Database::execute(
            "UPDATE goals SET status = 'paused', streak_count = 0 WHERE id = :id",
            [':id' => $goal['id']]
        );

        // Post publicly in channel
        $config = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        if ($config) {
            $displayName = Api::resolveDisplayName($interaction);
            $msg = "**{$displayName}** has paused their goal: **{$goal['name']}**. Check-ins are on hold.";
            Api::sendMessage($config['accountability_channel_id'], ['content' => $msg]);
        }

        return self::ephemeral("Goal paused. Your streak has been reset. Daily nudges will continue so you can unpause.");
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }
}
