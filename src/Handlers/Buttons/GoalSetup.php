<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Buttons;

use AccountaBuddy\Discord\Types;

class GoalSetup
{
    /**
     * Handles personality_select — user picked a personality.
     * Updates the message to show step 2: cadence select.
     */
    public static function handlePersonalitySelect(array $interaction): array
    {
        $personality = $interaction['data']['values'][0] ?? '';

        $personalityLabel = match ($personality) {
            Types::PERSONALITY_HYPE      => '🔥📣 Hype Coach',
            Types::PERSONALITY_DRY       => '📈📊 Dry Colleague',
            Types::PERSONALITY_SARCASTIC => '👀🦊 Sarcastic Friend',
            Types::PERSONALITY_HARSH     => '🗿💀 Harsh Critic',
            default                      => $personality,
        };

        return [
            'type' => Types::UPDATE_MESSAGE,
            'data' => [
                'flags'   => Types::FLAG_EPHEMERAL,
                'content' => "**Step 2 of 3 — Choose a cadence.**\nPersonality: {$personalityLabel}",
                'components' => [[
                    'type'       => Types::COMPONENT_ACTION_ROW,
                    'components' => [[
                        'type'        => Types::COMPONENT_STRING_SELECT,
                        'custom_id'   => "cadence_select:{$personality}",
                        'placeholder' => 'Choose a cadence...',
                        'options'     => [
                            ['label' => 'One-time',       'value' => 'one_time',   'description' => 'No repeat — just get it done.'],
                            ['label' => 'Daily',          'value' => 'daily',      'description' => 'Every single day.'],
                            ['label' => '1× per week',    'value' => 'weekly-1',   'description' => 'Once a week.'],
                            ['label' => '2× per week',    'value' => 'weekly-2',   'description' => 'Twice a week.'],
                            ['label' => '3× per week',    'value' => 'weekly-3',   'description' => '3 times a week.'],
                            ['label' => '4× per week',    'value' => 'weekly-4',   'description' => '4 times a week.'],
                            ['label' => '5× per week',    'value' => 'weekly-5',   'description' => '5 times a week (workdays).'],
                            ['label' => '6× per week',    'value' => 'weekly-6',   'description' => '6 times a week.'],
                            ['label' => '1× per month',   'value' => 'monthly-1',  'description' => 'Once a month (30-day cycle).'],
                            ['label' => '5× per month',   'value' => 'monthly-5',  'description' => '5 times per 30-day cycle.'],
                            ['label' => '10× per month',  'value' => 'monthly-10', 'description' => '10 times per 30-day cycle.'],
                            ['label' => '15× per month',  'value' => 'monthly-15', 'description' => '15 times per 30-day cycle.'],
                            ['label' => '20× per month',  'value' => 'monthly-20', 'description' => '20 times per 30-day cycle.'],
                        ],
                    ]],
                ]],
            ],
        ];
    }

    /**
     * Handles cadence_select:{personality} — user picked a cadence.
     * Opens the goal creation modal with personality+cadence encoded in custom_id.
     */
    public static function handleCadenceSelect(array $interaction, string $personality): array
    {
        $cadence = $interaction['data']['values'][0] ?? '';

        return [
            'type' => Types::MODAL,
            'data' => [
                'custom_id' => "goal_create:{$personality}:{$cadence}",
                'title'     => 'Step 3 of 3 — Name your goal',
                'components' => [
                    [
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [[
                            'type'        => Types::COMPONENT_TEXT_INPUT,
                            'custom_id'   => 'goal_name',
                            'label'       => 'Goal name',
                            'style'       => Types::TEXT_SHORT,
                            'required'    => true,
                            'max_length'  => 100,
                            'placeholder' => 'e.g. Run 3 miles',
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
                            'max_length'  => 300,
                            'placeholder' => 'Add any extra context...',
                        ]],
                    ],
                    [
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [[
                            'type'        => Types::COMPONENT_TEXT_INPUT,
                            'custom_id'   => 'goal_checkin_time',
                            'label'       => 'Check-in time (HH:MM, 24-hour UTC)',
                            'style'       => Types::TEXT_SHORT,
                            'required'    => true,
                            'min_length'  => 4,
                            'max_length'  => 5,
                            'placeholder' => '08:00',
                        ]],
                    ],
                ],
            ],
        ];
    }
}
