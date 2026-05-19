<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Buttons;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class Pause
{
    public static function handle(array $interaction, string $action, string $goalId): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $goal = Database::fetch("SELECT * FROM goals WHERE id = :id", [':id' => $goalId]);
        if (!$goal || $goal['user_id'] !== $userId) {
            return self::ephemeral("These buttons aren't for you.");
        }

        $config      = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        $channelId   = $config['accountability_channel_id'] ?? null;
        $displayName = Api::resolveDisplayName($interaction);
        $vars        = ['name' => $displayName, 'goal' => $goal['name']];

        return match ($action) {
            'unpause_did'    => self::handleUnpauseDid($goal, $channelId, $vars),
            'unpause_going'  => self::handleUnpauseGoing($goal, $channelId, $vars),
            'cancel_goal'    => self::handleCancel($goal, $channelId, $vars),
            default          => self::ephemeral("Unknown action."),
        };
    }

    private static function handleUnpauseDid(array $goal, ?string $channelId, array $vars): array
    {
        // Unpause and count as a completion
        Database::execute(
            "UPDATE goals SET status = 'active' WHERE id = :id",
            [':id' => $goal['id']]
        );

        // Mark any pending checkin complete
        Database::execute(
            "UPDATE checkins SET status = 'complete', responded_at = NOW()
              WHERE goal_id = :gid AND status = 'pending'
              ORDER BY scheduled_at DESC LIMIT 1",
            [':gid' => $goal['id']]
        );

        // Update cycle completions (if not one-time goal)
        if ($goal['cadence_type'] !== Types::CADENCE_ONE_TIME) {
            $cycle = Database::fetch(
                "SELECT * FROM cycles WHERE goal_id = :gid AND status = 'active' ORDER BY start_date DESC LIMIT 1",
                [':gid' => $goal['id']]
            );
            if (!$cycle) {
                $today = gmdate('Y-m-d');
                $cycleEnd = \AccountaBuddy\Handlers\Modals\GoalCreate::cycleEndDate($goal['cadence_type'], $today);
                Database::insert('cycles', [
                    'goal_id'     => $goal['id'],
                    'start_date'  => $today,
                    'end_date'    => $cycleEnd,
                    'target'      => $goal['cadence_target'],
                    'completions' => 0,
                    'status'      => Types::CYCLE_ACTIVE,
                ]);
                $cycle = Database::fetch(
                    "SELECT * FROM cycles WHERE goal_id = :gid AND status = 'active' ORDER BY start_date DESC LIMIT 1",
                    [':gid' => $goal['id']]
                );
            }
            if ($cycle) {
                Database::execute(
                    "UPDATE cycles SET completions = completions + 1 WHERE id = :id",
                    [':id' => $cycle['id']]
                );
            }
        }

        // For daily goals, update streak immediately
        if ($goal['cadence_type'] === Types::CADENCE_DAILY) {
            $newStreak = (int)$goal['streak_count'] + 1;
            $newBest   = max($newStreak, (int)$goal['streak_best']);
            Database::execute(
                "UPDATE goals SET streak_count = :s, streak_best = :b WHERE id = :id",
                [':s' => $newStreak, ':b' => $newBest, ':id' => $goal['id']]
            );
            $displayName = $vars['name'] ?? 'User';
            CheckIn::checkMilestone($goal, $newStreak, $channelId, $displayName);
        }

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'comeback', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return self::ephemeral("Goal unpaused and check-in recorded!");
    }

    private static function handleUnpauseGoing(array $goal, ?string $channelId, array $vars): array
    {
        Database::execute(
            "UPDATE goals SET status = 'active' WHERE id = :id",
            [':id' => $goal['id']]
        );

        // Mark today's pending check-in as skipped so it doesn't trigger escalation/miss
        Database::execute(
            "UPDATE checkins SET status = 'skipped', responded_at = NOW()
              WHERE goal_id = :gid AND status = 'pending'
              ORDER BY scheduled_at DESC LIMIT 1",
            [':gid' => $goal['id']]
        );

        // Open new active cycle if none exists
        if ($goal['cadence_type'] !== Types::CADENCE_ONE_TIME) {
            $cycle = Database::fetch(
                "SELECT * FROM cycles WHERE goal_id = :gid AND status = 'active' LIMIT 1",
                [':gid' => $goal['id']]
            );
            if (!$cycle) {
                $today = gmdate('Y-m-d');
                $cycleEnd = \AccountaBuddy\Handlers\Modals\GoalCreate::cycleEndDate($goal['cadence_type'], $today);
                Database::insert('cycles', [
                    'goal_id'     => $goal['id'],
                    'start_date'  => $today,
                    'end_date'    => $cycleEnd,
                    'target'      => $goal['cadence_target'],
                    'completions' => 0,
                    'status'      => Types::CYCLE_ACTIVE,
                ]);
            }
        }

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'comeback', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return self::ephemeral("Goal unpaused! Check-ins will resume at your usual time.");
    }

    private static function handleCancel(array $goal, ?string $channelId, array $vars): array
    {
        Database::execute(
            "UPDATE goals SET status = 'cancelled', cancelled_at = NOW() WHERE id = :id",
            [':id' => $goal['id']]
        );

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'cancel', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return self::ephemeral("Goal cancelled.");
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }
}
