<?php

declare(strict_types=1);

namespace AccountaBuddy\Cron;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;
use AccountaBuddy\Handlers\Buttons\CheckIn as CheckInHandler;
use AccountaBuddy\Handlers\Modals\GoalCreate;

class CycleEvaluator
{
    public function run(): void
    {
        $today = gmdate('Y-m-d');

        // Find cycles that ended yesterday or earlier and are still active
        $cycles = Database::fetchAll(
            "SELECT cy.*, g.personality, g.name AS goal_name, g.user_id, g.id AS goal_id,
                    g.guild_id, g.streak_count, g.streak_best, g.cadence_type,
                    gm.display_name, sc.accountability_channel_id
               FROM cycles cy
               JOIN goals g ON g.id = cy.goal_id
               JOIN guild_members gm ON gm.guild_id = g.guild_id AND gm.user_id = g.user_id
               JOIN server_config sc ON sc.guild_id = g.guild_id
              WHERE cy.status = 'active'
                AND cy.end_date < :today
                AND g.status IN ('active', 'paused', 'on_hold')",
            [':today' => $today]
        );

        foreach ($cycles as $cycle) {
            try {
                $this->evaluateCycle($cycle);
            } catch (\Throwable $e) {
                error_log("CycleEvaluator error for cycle {$cycle['id']}: " . $e->getMessage());
            }
        }
    }

    private function evaluateCycle(array $cycle): void
    {
        $hit     = (int)$cycle['completions'] >= (int)$cycle['target'];
        $goalId  = $cycle['goal_id'];

        if ($hit) {
            // Update streak
            $newStreak = (int)$cycle['streak_count'] + 1;
            $newBest   = max($newStreak, (int)$cycle['streak_best']);

            Database::execute(
                "UPDATE goals SET streak_count = :s, streak_best = :b WHERE id = :id",
                [':s' => $newStreak, ':b' => $newBest, ':id' => $goalId]
            );
            Database::execute(
                "UPDATE cycles SET status = 'completed' WHERE id = :id",
                [':id' => $cycle['id']]
            );

            // Check milestone
            $fakeGoal = [
                'id'          => $goalId,
                'personality' => $cycle['personality'],
                'name'        => $cycle['goal_name'],
                'cadence_type'=> $cycle['cadence_type'],
                'streak_count'=> $newStreak,
                'streak_best' => $newBest,
            ];
            CheckInHandler::checkMilestone(
                $fakeGoal,
                $newStreak,
                $cycle['accountability_channel_id'],
                $cycle['display_name']
            );
        } else {
            // Miss: break streak
            Database::execute(
                "UPDATE goals SET streak_count = 0 WHERE id = :id",
                [':id' => $goalId]
            );
            Database::execute(
                "UPDATE cycles SET status = 'missed' WHERE id = :id",
                [':id' => $cycle['id']]
            );

            if ((int)$cycle['streak_count'] > 0 && $cycle['accountability_channel_id']) {
                $vars = ['name' => $cycle['display_name'], 'goal' => $cycle['goal_name']];
                $msg  = Library::get($cycle['personality'], 'streak_break', $vars);
                Api::sendMessage($cycle['accountability_channel_id'], ['content' => $msg]);
            }
        }

        // Open next cycle (if goal still active)
        $goal = Database::fetch("SELECT status FROM goals WHERE id = :id", [':id' => $goalId]);
        if ($goal && $goal['status'] === Types::GOAL_ACTIVE) {
            $newStart = gmdate('Y-m-d', strtotime($cycle['end_date'] . ' +1 day'));
            $newEnd   = GoalCreate::cycleEndDate($cycle['cadence_type'], $newStart);

            Database::execute(
                "UPDATE goals SET cycle_start_date = :sd WHERE id = :id",
                [':sd' => $newStart, ':id' => $goalId]
            );
            Database::execute(
                "UPDATE goals SET overachiever_finish_out = FALSE WHERE id = :id",
                [':id' => $goalId]
            );

            Database::insert('cycles', [
                'goal_id'    => $goalId,
                'start_date' => $newStart,
                'end_date'   => $newEnd,
                'target'     => $cycle['target'],
                'completions' => 0,
                'status'     => Types::CYCLE_ACTIVE,
            ]);
        }
    }
}
