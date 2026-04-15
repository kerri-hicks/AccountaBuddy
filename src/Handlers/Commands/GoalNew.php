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

        // Step 1: show personality select
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => [
                'flags'   => Types::FLAG_EPHEMERAL,
                'content' => "**Step 1 of 3 — Choose a personality for this goal.**\n_This cannot be changed later._",
                'components' => [[
                    'type'       => Types::COMPONENT_ACTION_ROW,
                    'components' => [[
                        'type'        => Types::COMPONENT_STRING_SELECT,
                        'custom_id'   => 'personality_select',
                        'placeholder' => 'Choose a personality...',
                        'options'     => [
                            [
                                'label'       => '🔥📣 Hype Coach',
                                'value'       => Types::PERSONALITY_HYPE,
                                'description' => 'I believe in you unconditionally.',
                            ],
                            [
                                'label'       => '📈📊 Dry Colleague',
                                'value'       => Types::PERSONALITY_DRY,
                                'description' => "I'm tracking this. Results pending.",
                            ],
                            [
                                'label'       => '👀🦊 Sarcastic Friend',
                                'value'       => Types::PERSONALITY_SARCASTIC,
                                'description' => 'Oh wow, you actually did it. Bold.',
                            ],
                            [
                                'label'       => '🗿💀 Harsh Critic',
                                'value'       => Types::PERSONALITY_HARSH,
                                'description' => 'There is no winning with this one.',
                            ],
                        ],
                    ]],
                ]],
            ],
        ];
    }
}
