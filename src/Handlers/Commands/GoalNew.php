<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Types;

class GoalNew
{
    public static function handle(array $interaction): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        // Check active goal count
        $count = Database::fetch(
            "SELECT COUNT(*) AS cnt FROM goals WHERE user_id = :uid AND guild_id = :gid AND status IN ('active','paused','on_hold')",
            [':uid' => $userId, ':gid' => $guildId]
        );

        if ((int)($count['cnt'] ?? 0) >= 5) {
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => [
                    'content' => "You already have 5 active goals. Cancel or complete one before adding a new one.",
                    'flags'   => Types::FLAG_EPHEMERAL,
                ],
            ];
        }

        // Return modal
        return [
            'type' => Types::MODAL,
            'data' => [
                'custom_id' => 'goal_create',
                'title'     => 'Create a New Goal',
                'components' => [
                    [
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [[
                            'type'        => Types::COMPONENT_TEXT_INPUT,
                            'custom_id'   => 'goal_name',
                            'label'       => 'Goal name',
                            'style'       => Types::TEXT_SHORT,
                            'required'    => true,
                            'max_length'  => 80,
                            'placeholder' => "Keep it short and specific. e.g. 'Run 2 miles' or 'Practice guitar'",
                        ]],
                    ],
                    [
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [[
                            'type'        => Types::COMPONENT_TEXT_INPUT,
                            'custom_id'   => 'goal_description',
                            'label'       => 'Description (optional)',
                            'style'       => Types::TEXT_PARAGRAPH,
                            'required'    => false,
                            'max_length'  => 500,
                        ]],
                    ],
                    [
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [[
                            'type'        => Types::COMPONENT_TEXT_INPUT,
                            'custom_id'   => 'goal_cadence',
                            'label'       => 'Cadence',
                            'style'       => Types::TEXT_SHORT,
                            'required'    => true,
                            'placeholder' => 'daily | weekly-1 | weekly-2 | weekly-3 | monthly-1 | monthly-X | one-time',
                        ]],
                    ],
                    [
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [[
                            'type'        => Types::COMPONENT_TEXT_INPUT,
                            'custom_id'   => 'goal_checkin_time',
                            'label'       => 'Check-in time (HH:MM, 24h UTC)',
                            'style'       => Types::TEXT_SHORT,
                            'required'    => true,
                            'placeholder' => 'e.g. 08:00 — all times are UTC',
                            'max_length'  => 5,
                        ]],
                    ],
                    [
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [[
                            'type'        => Types::COMPONENT_TEXT_INPUT,
                            'custom_id'   => 'goal_personality',
                            'label'       => 'Personality (cannot change later)',
                            'style'       => Types::TEXT_SHORT,
                            'required'    => true,
                            'placeholder' => 'hype | dry | sarcastic | harsh',
                            'max_length'  => 20,
                        ]],
                    ],
                ],
            ],
        ];
    }
}
