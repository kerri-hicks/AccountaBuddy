<?php

declare(strict_types=1);

namespace AccountaBuddy\Cron;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class CheckInScheduler
{
    public function run(): void
    {
        $nowTime = gmdate('H:i:00'); // Current UTC time, HH:MM:00

        // Find all active goals whose checkin_time matches the current minute
        $goals = Database::fetchAll(
            "SELECT g.*, sc.accountability_channel_id, gm.display_name
               FROM goals g
               JOIN server_config sc ON sc.guild_id = g.guild_id
               JOIN guild_members gm ON gm.guild_id = g.guild_id AND gm.user_id = g.user_id
              WHERE g.status IN ('active', 'paused')
                AND g.checkin_time = :t",
            [':t' => $nowTime]
        );

        foreach ($goals as $goal) {
            try {
                $this->processGoal($goal);
            } catch (\Throwable $e) {
                error_log("CheckInScheduler error for goal {$goal['id']}: " . $e->getMessage());
            }
        }
    }

    private function processGoal(array $goal): void
    {
        $today      = gmdate('Y-m-d');
        $channelId  = $goal['accountability_channel_id'];
        $displayName = $goal['display_name'];

        // Avoid double-firing (if cron runs twice in same minute)
        $existing = Database::fetch(
            "SELECT id FROM checkins WHERE goal_id = :gid AND cycle_date = :d",
            [':gid' => $goal['id'], ':d' => $today]
        );
        if ($existing) return;

        // Insert checkin record
        $checkinId = Database::insert('checkins', [
            'goal_id'      => $goal['id'],
            'scheduled_at' => gmdate('Y-m-d H:i:s'),
            'status'       => Types::CHECKIN_PENDING,
            'escalation_level' => 0,
            'cycle_date'   => $today,
        ]);

        if ($goal['cadence_type'] === Types::CADENCE_ONE_TIME) {
            $this->sendOneTimeReminder($goal, $channelId, $displayName, $checkinId);
        } elseif ($goal['cadence_type'] === Types::CADENCE_DAILY) {
            $this->sendPublicCheckin($goal, $channelId, $displayName, $checkinId);
        } else {
            // Weekly / monthly: ephemeral nudge only (sent via DM since we can't post ephemeral from cron)
            $this->sendEphemeralNudge($goal, $displayName);
        }
    }

    private function sendOneTimeReminder(array $goal, string $channelId, string $displayName, string $checkinId): void
    {
        // Increment reminder count
        Database::execute(
            "UPDATE goals SET reminder_count = reminder_count + 1 WHERE id = :id",
            [':id' => $goal['id']]
        );
        $dayCount = (int)$goal['reminder_count'] + 1;

        $vars = ['name' => $displayName, 'goal' => $goal['name'], 'N' => $dayCount];
        $msg  = Library::get($goal['personality'], 'one_time_reminder', $vars);

        $posted = Api::sendMessage($channelId, [
            'content'    => $msg,
            'components' => [[
                'type'       => Types::COMPONENT_ACTION_ROW,
                'components' => [
                    [
                        'type'      => Types::COMPONENT_BUTTON,
                        'style'     => Types::BUTTON_SUCCESS,
                        'label'     => '✅ I did it',
                        'custom_id' => "one_time_did_it:{$goal['id']}",
                    ],
                    [
                        'type'      => Types::COMPONENT_BUTTON,
                        'style'     => Types::BUTTON_DANGER,
                        'label'     => '❌ Cancel this goal',
                        'custom_id' => "one_time_cancel:{$goal['id']}",
                    ],
                ],
            ]],
        ]);

        Database::execute(
            "UPDATE checkins SET discord_message_id = :mid WHERE id = :id",
            [':mid' => $posted['id'] ?? null, ':id' => $checkinId]
        );
    }

    private function sendPublicCheckin(array $goal, string $channelId, string $displayName, string $checkinId): void
    {
        $streakLine = (int)$goal['streak_count'] > 0
            ? "\n[Streak: {$goal['streak_count']} " . ($goal['streak_count'] === 1 ? 'cycle' : 'cycles') . "]"
            : '';

        $personalityIcon = match ($goal['personality']) {
            Types::PERSONALITY_HYPE      => '🔥📣',
            Types::PERSONALITY_DRY       => '📈📊',
            Types::PERSONALITY_SARCASTIC => '👀🦊',
            Types::PERSONALITY_HARSH     => '🗿💀',
            default                      => '',
        };

        $content = "{$personalityIcon} Time to check in, <@{$goal['user_id']}>!\n"
                 . "Goal: **{$goal['name']}**"
                 . $streakLine;

        $posted = Api::sendMessage($channelId, [
            'content'    => $content,
            'components' => [[
                'type'       => Types::COMPONENT_ACTION_ROW,
                'components' => [
                    [
                        'type'      => Types::COMPONENT_BUTTON,
                        'style'     => Types::BUTTON_SUCCESS,
                        'label'     => '✅ I did it',
                        'custom_id' => "checkin_did_it:{$goal['id']}",
                    ],
                    [
                        'type'      => Types::COMPONENT_BUTTON,
                        'style'     => Types::BUTTON_SECONDARY,
                        'label'     => '⏳ Not yet — I\'ll do it later',
                        'custom_id' => "checkin_not_yet:{$goal['id']}",
                    ],
                    [
                        'type'      => Types::COMPONENT_BUTTON,
                        'style'     => Types::BUTTON_DANGER,
                        'label'     => '⏭️ Skipping this one',
                        'custom_id' => "checkin_skipping:{$goal['id']}",
                    ],
                    [
                        'type'      => Types::COMPONENT_BUTTON,
                        'style'     => Types::BUTTON_DANGER,
                        'label'     => '❌ Cancel this goal',
                        'custom_id' => "cancel_confirm:{$goal['id']}",
                    ],
                ],
            ]],
        ]);

        Database::execute(
            "UPDATE checkins SET discord_message_id = :mid WHERE id = :id",
            [':mid' => $posted['id'] ?? null, ':id' => $checkinId]
        );
    }

    private function sendEphemeralNudge(array $goal, string $displayName): void
    {
        // For weekly/monthly cadences, send a DM instead of public post
        // (Ephemeral messages can only be sent in response to interactions, not from a bot directly)
        try {
            Api::sendDm($goal['user_id'], [
                'content'    => "⏰ Time to check in on **{$goal['name']}**! Did you do it today?",
                'components' => [[
                    'type'       => Types::COMPONENT_ACTION_ROW,
                    'components' => [
                        [
                            'type'      => Types::COMPONENT_BUTTON,
                            'style'     => Types::BUTTON_SUCCESS,
                            'label'     => '✅ I did it today',
                            'custom_id' => "checkin_did_it:{$goal['id']}",
                        ],
                        [
                            'type'      => Types::COMPONENT_BUTTON,
                            'style'     => Types::BUTTON_SECONDARY,
                            'label'     => '⏭️ Not today',
                            'custom_id' => "checkin_not_yet:{$goal['id']}",
                        ],
                    ],
                ]],
            ]);
        } catch (\Throwable $e) {
            error_log("Failed to send DM nudge to {$goal['user_id']}: " . $e->getMessage());
        }
    }
}
