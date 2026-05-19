<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Buttons;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Handlers\Modals\GoalCreate;

class EarlyCycle
{
    public static function handle(array $interaction, string $action, string $goalId): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $goal = Database::fetch("SELECT * FROM goals WHERE id = :id", [':id' => $goalId]);
        if (!$goal || $goal['user_id'] !== $userId) {
            return self::ephemeral("These buttons aren't for you.");
        }

        $config    = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        $channelId = $config['accountability_channel_id'] ?? null;
        $displayName = Api::resolveDisplayName($interaction);

        return match ($action) {
            'early_new_cycle'   => self::handleNewCycle($goal, $channelId, $displayName),
            'early_finish_out'  => self::handleFinishOut($goal, $channelId, $displayName),
            default             => self::ephemeral("Unknown action."),
        };
    }

    private static function handleNewCycle(array $goal, ?string $channelId, string $displayName): array
    {
        // Close current cycle as completed
        $cycle = Database::fetch(
            "SELECT * FROM cycles WHERE goal_id = :gid AND status = 'active' ORDER BY start_date DESC LIMIT 1",
            [':gid' => $goal['id']]
        );

        if ($cycle) {
            Database::execute(
                "UPDATE cycles SET status = 'completed', end_date = CURRENT_DATE WHERE id = :id",
                [':id' => $cycle['id']]
            );

            // Update streak
            $newStreak = (int)$goal['streak_count'] + 1;
            $newBest   = max($newStreak, (int)$goal['streak_best']);
            Database::execute(
                "UPDATE goals SET streak_count = :s, streak_best = :b, cycle_start_date = CURRENT_DATE WHERE id = :id",
                [':s' => $newStreak, ':b' => $newBest, ':id' => $goal['id']]
            );

            // Open new cycle
            $newEnd = GoalCreate::cycleEndDate($goal['cadence_type'], date('Y-m-d'));
            Database::insert('cycles', [
                'goal_id'    => $goal['id'],
                'start_date' => date('Y-m-d'),
                'end_date'   => $newEnd,
                'target'     => $goal['cadence_target'],
                'completions' => 0,
                'status'     => Types::CYCLE_ACTIVE,
            ]);
        }

        if ($channelId) {
            Api::sendMessage($channelId, [
                'content' => "🔄 {$displayName} started a new cycle early for **{$goal['name']}**! Fresh slate, same energy.",
            ]);
        }

        return self::ephemeral("New cycle started! Keep the momentum going.");
    }

    private static function handleFinishOut(array $goal, ?string $channelId, string $displayName): array
    {
        Database::execute(
            "UPDATE goals SET overachiever_finish_out = TRUE WHERE id = :id",
            [':id' => $goal['id']]
        );

        if ($channelId) {
            Api::sendMessage($channelId, [
                'content' => "⏳ {$displayName} is finishing out the current cycle for **{$goal['name']}**. Daily check-ins continue!",
            ]);
        }

        return self::ephemeral("Got it — finishing out the cycle. Daily nudges will continue.");
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }
}
