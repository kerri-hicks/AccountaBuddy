<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers;

use AccountaBuddy\Discord\Types;
use AccountaBuddy\Handlers\Commands\GoalNew;
use AccountaBuddy\Handlers\Commands\GoalList;
use AccountaBuddy\Handlers\Commands\GoalView;
use AccountaBuddy\Handlers\Commands\GoalPause;
use AccountaBuddy\Handlers\Commands\GoalCancel;
use AccountaBuddy\Handlers\Commands\GoalAppeal;
use AccountaBuddy\Handlers\Commands\Setup;
use AccountaBuddy\Handlers\Commands\Leaderboard;
use AccountaBuddy\Handlers\Modals\GoalCreate;
use AccountaBuddy\Handlers\Buttons\CheckIn;
use AccountaBuddy\Handlers\Buttons\OneTime;
use AccountaBuddy\Handlers\Buttons\Pause;
use AccountaBuddy\Handlers\Buttons\EarlyCycle;
use AccountaBuddy\Handlers\Buttons\Appeal;
use AccountaBuddy\Handlers\Buttons\Hold;
use AccountaBuddy\Handlers\Buttons\CancelConfirm;
use AccountaBuddy\Handlers\Buttons\GoalSetup;

class InteractionRouter
{
    public function __construct(private array $interaction) {}

    public function dispatch(): array
    {
        return match ($this->interaction['type']) {
            Types::APPLICATION_COMMAND => $this->handleCommand(),
            Types::MESSAGE_COMPONENT   => $this->handleComponent(),
            Types::MODAL_SUBMIT        => $this->handleModal(),
            default                    => $this->unknown(),
        };
    }

    private function handleCommand(): array
    {
        $name = $this->interaction['data']['name'] ?? '';
        $sub  = $this->interaction['data']['options'][0]['name'] ?? '';

        return match ($name) {
            'goal-abuddy' => match ($sub) {
                'new'    => GoalNew::handle($this->interaction),
                'list'   => GoalList::handle($this->interaction),
                'view'   => GoalView::handle($this->interaction),
                'pause'  => GoalPause::handle($this->interaction),
                'cancel' => GoalCancel::handle($this->interaction),
                'appeal' => GoalAppeal::handle($this->interaction),
                default  => $this->unknown(),
            },
            'accountabuddy' => match ($sub) {
                'setup'       => Setup::handle($this->interaction),
                'leaderboard' => Leaderboard::handle($this->interaction),
                default       => $this->unknown(),
            },
            default => $this->unknown(),
        };
    }

    private function handleComponent(): array
    {
        $customId = $this->interaction['data']['custom_id'] ?? '';
        $parts    = explode(':', $customId, 3);
        $action   = $parts[0];
        $param1   = $parts[1] ?? '';
        $param2   = $parts[2] ?? '';

        return match (true) {
            // Check-in buttons
            in_array($action, ['checkin_did_it', 'checkin_not_yet', 'checkin_skipping'], true)
                => CheckIn::handle($this->interaction, $action, $param1),

            // One-time goal buttons
            in_array($action, ['one_time_did_it', 'one_time_cancel'], true)
                => OneTime::handle($this->interaction, $action, $param1),

            // Pause/unpause buttons
            in_array($action, ['unpause_did', 'unpause_going'], true)
                => Pause::handle($this->interaction, $action, $param1),

            // Cancel confirmation
            in_array($action, ['cancel_confirm', 'cancel_abort'], true)
                => CancelConfirm::handle($this->interaction, $action, $param1),

            // Early cycle
            in_array($action, ['early_new_cycle', 'early_finish_out'], true)
                => EarlyCycle::handle($this->interaction, $action, $param1),

            // Hold DM buttons
            in_array($action, ['hold_keep', 'hold_cancel'], true)
                => Hold::handle($this->interaction, $action, $param1),

            // Appeal vote
            $action === 'appeal_vote'
                => Appeal::handle($this->interaction, $param1),

            // Pause flow cancel button (reuse cancel confirm)
            $action === 'cancel_goal'
                => CancelConfirm::handle($this->interaction, 'cancel_confirm', $param1),

            // Goal creation multi-step selects
            $action === 'personality_select'
                => GoalSetup::handlePersonalitySelect($this->interaction),
            $action === 'cadence_select'
                => GoalSetup::handleCadenceSelect($this->interaction, $param1),

            default => $this->unknown(),
        };
    }

    private function handleModal(): array
    {
        $customId = $this->interaction['data']['custom_id'] ?? '';

        if (str_starts_with($customId, 'goal_create')) {
            return GoalCreate::handle($this->interaction);
        }

        return $this->unknown();
    }

    private function unknown(): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => [
                'content' => "Unknown interaction.",
                'flags'   => Types::FLAG_EPHEMERAL,
            ],
        ];
    }
}
