<?php

declare(strict_types=1);

namespace AccountaBuddy\Cron;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class EscalationRunner
{
    public function run(): void
    {
        $this->escalateStep2();
        $this->processStep3Misses();
    }

    /**
     * Step 2: +4h no response → public escalation post (next personality up)
     */
    private function escalateStep2(): void
    {
        $fourHoursAgo = gmdate('Y-m-d H:i:s', time() - 4 * 3600);

        $checkins = Database::fetchAll(
            "SELECT c.*, g.personality, g.name AS goal_name, g.user_id, g.id AS goal_id, g.guild_id,
                    gm.display_name, sc.accountability_channel_id
               FROM checkins c
               JOIN goals g ON g.id = c.goal_id
               JOIN guild_members gm ON gm.guild_id = g.guild_id AND gm.user_id = g.user_id
               JOIN server_config sc ON sc.guild_id = g.guild_id
              WHERE c.status = 'pending'
                AND c.escalation_level = 0
                AND c.scheduled_at <= :t
                AND g.cadence_type = 'daily'",
            [':t' => $fourHoursAgo]
        );

        foreach ($checkins as $checkin) {
            try {
                $nextPersonality = Types::ESCALATION_ORDER[$checkin['personality']] ?? $checkin['personality'];

                // Sample escalation messages per spec
                $msgs = [
                    Types::PERSONALITY_HYPE      => "Check-in for <@{$checkin['user_id']}> not yet recorded. Still waiting.",
                    Types::PERSONALITY_DRY       => "Hey <@{$checkin['user_id']}>, still waiting — did this happen or not?",
                    Types::PERSONALITY_SARCASTIC => "<@{$checkin['user_id']}>. The check-in. It's been four hours. Well?",
                    Types::PERSONALITY_HARSH     => "Hey <@{$checkin['user_id']}>! We know you've got this — just let us know! 🔥",
                ];

                $content = $msgs[$nextPersonality] ?? "Hey <@{$checkin['user_id']}>, still waiting on your check-in for **{$checkin['goal_name']}**.";

                Api::sendMessage($checkin['accountability_channel_id'], [
                    'content'    => $content,
                    'components' => [[
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [
                            [
                                'type'      => Types::COMPONENT_BUTTON,
                                'style'     => Types::BUTTON_SUCCESS,
                                'label'     => '✅ I did it',
                                'custom_id' => "checkin_did_it:{$checkin['goal_id']}",
                            ],
                            [
                                'type'      => Types::COMPONENT_BUTTON,
                                'style'     => Types::BUTTON_DANGER,
                                'label'     => '⏭️ Skipping',
                                'custom_id' => "checkin_skipping:{$checkin['goal_id']}",
                            ],
                        ],
                    ]],
                ]);

                Database::execute(
                    "UPDATE checkins SET escalation_level = 1 WHERE id = :id",
                    [':id' => $checkin['id']]
                );
            } catch (\Throwable $e) {
                error_log("EscalationRunner step2 error for checkin {$checkin['id']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Step 3: +24h still no response → mark missed, break streak
     * Step 4: Second consecutive miss → place on hold, send DM
     */
    private function processStep3Misses(): void
    {
        $twentyFourHoursAgo = gmdate('Y-m-d H:i:s', time() - 24 * 3600);

        $checkins = Database::fetchAll(
            "SELECT c.*, g.personality, g.name AS goal_name, g.user_id, g.id AS goal_id,
                    g.guild_id, g.streak_count, gm.display_name, sc.accountability_channel_id
               FROM checkins c
               JOIN goals g ON g.id = c.goal_id
               JOIN guild_members gm ON gm.guild_id = g.guild_id AND gm.user_id = g.user_id
               JOIN server_config sc ON sc.guild_id = g.guild_id
              WHERE c.status = 'pending'
                AND c.scheduled_at <= :t",
            [':t' => $twentyFourHoursAgo]
        );

        foreach ($checkins as $checkin) {
            try {
                // Mark missed
                Database::execute(
                    "UPDATE checkins SET status = 'missed', escalation_level = 2, responded_at = NOW() WHERE id = :id",
                    [':id' => $checkin['id']]
                );

                // Break streak
                Database::execute(
                    "UPDATE goals SET streak_count = 0 WHERE id = :id",
                    [':id' => $checkin['goal_id']]
                );

                $vars = [
                    'name'   => $checkin['display_name'],
                    'goal'   => $checkin['goal_name'],
                    'streak' => $checkin['streak_count'],
                ];

                // Post miss message
                $missMsg = Library::get($checkin['personality'], 'miss', $vars);
                Api::sendMessage($checkin['accountability_channel_id'], ['content' => $missMsg]);

                // Also post streak break if they had a streak
                if ((int)$checkin['streak_count'] > 0) {
                    $breakMsg = Library::get($checkin['personality'], 'streak_break', $vars);
                    Api::sendMessage($checkin['accountability_channel_id'], ['content' => $breakMsg]);
                }

                // Check for second consecutive miss
                $consecutiveMisses = $this->countConsecutiveMisses($checkin['goal_id']);
                if ($consecutiveMisses >= 2) {
                    $this->placeOnHold($checkin);
                }
            } catch (\Throwable $e) {
                error_log("EscalationRunner step3 error for checkin {$checkin['id']}: " . $e->getMessage());
            }
        }
    }

    private function countConsecutiveMisses(string $goalId): int
    {
        $recent = Database::fetchAll(
            "SELECT status FROM checkins WHERE goal_id = :gid AND status NOT IN ('pending')
              ORDER BY scheduled_at DESC LIMIT 3",
            [':gid' => $goalId]
        );

        $count = 0;
        foreach ($recent as $c) {
            if (in_array($c['status'], ['missed', 'skipped'], true)) {
                $count++;
            } else {
                break;
            }
        }
        return $count;
    }

    private function placeOnHold(array $checkin): void
    {
        Database::execute(
            "UPDATE goals SET status = 'on_hold' WHERE id = :id",
            [':id' => $checkin['goal_id']]
        );

        // Aggressive public callout
        $callout = "🚨 <@{$checkin['user_id']}> has missed two check-ins in a row on **{$checkin['goal_name']}**. "
                 . "Goal placed on hold. Check your DMs.";
        Api::sendMessage($checkin['accountability_channel_id'], ['content' => $callout]);

        // Private DM with options
        try {
            Api::sendDm($checkin['user_id'], [
                'content'    => "Your goal **{$checkin['goal_name']}** has been placed on hold after two missed check-ins.\n\nWhat would you like to do?",
                'components' => [[
                    'type'       => Types::COMPONENT_ACTION_ROW,
                    'components' => [
                        [
                            'type'      => Types::COMPONENT_BUTTON,
                            'style'     => Types::BUTTON_SUCCESS,
                            'label'     => '💪 Keep going',
                            'custom_id' => "hold_keep:{$checkin['goal_id']}",
                        ],
                        [
                            'type'      => Types::COMPONENT_BUTTON,
                            'style'     => Types::BUTTON_DANGER,
                            'label'     => '❌ Cancel this goal',
                            'custom_id' => "hold_cancel:{$checkin['goal_id']}",
                        ],
                    ],
                ]],
            ]);
        } catch (\Throwable $e) {
            error_log("Failed to send hold DM to {$checkin['user_id']}: " . $e->getMessage());
        }
    }
}
