<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Types;

class GoalCancel
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
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => ['content' => "Goal not found.", 'flags' => Types::FLAG_EPHEMERAL],
            ];
        }

        if (in_array($goal['status'], [Types::GOAL_CANCELLED, Types::GOAL_COMPLETED], true)) {
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => ['content' => "This goal is already {$goal['status']}.", 'flags' => Types::FLAG_EPHEMERAL],
            ];
        }

        // Show confirmation with buttons
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => [
                'content' => "Are you sure you want to cancel **{$goal['name']}**? This cannot be undone.",
                'flags'   => Types::FLAG_EPHEMERAL,
                'components' => [[
                    'type'       => Types::COMPONENT_ACTION_ROW,
                    'components' => [
                        [
                            'type'      => Types::COMPONENT_BUTTON,
                            'style'     => Types::BUTTON_DANGER,
                            'label'     => 'Yes, cancel it',
                            'custom_id' => "cancel_confirm:{$goal['id']}",
                        ],
                        [
                            'type'      => Types::COMPONENT_BUTTON,
                            'style'     => Types::BUTTON_SECONDARY,
                            'label'     => 'Never mind',
                            'custom_id' => "cancel_abort:{$goal['id']}",
                        ],
                    ],
                ]],
            ],
        ];
    }
}
