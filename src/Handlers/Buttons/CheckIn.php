<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Buttons;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;
use AccountaBuddy\Handlers\Modals\GoalCreate;

class CheckIn
{
    public static function handle(array $interaction, string $action, string $goalId): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $goal = Database::fetch("SELECT * FROM goals WHERE id = :id", [':id' => $goalId]);

        if (!$goal || $goal['user_id'] !== $userId) {
            return self::ephemeral("These buttons aren't for you.");
        }

        $config = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        $channelId = $config['accountability_channel_id'] ?? null;
        $displayName = Api::resolveDisplayName($interaction);

        // Update display name
        self::upsertMember($userId, $guildId, $displayName, $interaction);

        $vars = ['name' => $displayName, 'goal' => $goal['name'], 'streak' => $goal['streak_count']];

        return match ($action) {
            'checkin_did_it'  => self::handleDidIt($goal, $userId, $guildId, $channelId, $displayName, $vars),
            'checkin_not_yet' => self::handleNotYet($goal, $channelId, $displayName),
            'checkin_skipping'=> self::handleSkipping($goal, $channelId, $vars),
            default            => self::ephemeral("Unknown action."),
        };
    }

    private static function handleDidIt(array $goal, string $userId, string $guildId, ?string $channelId, string $displayName, array $vars): array
    {
        // Mark latest pending check-in as complete
        $checkin = Database::fetch(
            "SELECT * FROM checkins WHERE goal_id = :gid AND status = 'pending' ORDER BY scheduled_at DESC LIMIT 1",
            [':gid' => $goal['id']]
        );
        if ($checkin) {
            Database::execute(
                "UPDATE checkins SET status = 'complete', responded_at = NOW() WHERE id = :id",
                [':id' => $checkin['id']]
            );
        }

        // Update cycle completions
        $cycle = Database::fetch(
            "SELECT * FROM cycles WHERE goal_id = :gid AND status = 'active' ORDER BY start_date DESC LIMIT 1",
            [':gid' => $goal['id']]
        );

        $isOverachiever = false;
        if ($cycle) {
            Database::execute(
                "UPDATE cycles SET completions = completions + 1 WHERE id = :id",
                [':id' => $cycle['id']]
            );
            $newCompletions = (int)$cycle['completions'] + 1;
            if ($newCompletions >= (int)$cycle['target']) {
                $isOverachiever = $newCompletions > (int)$cycle['target'];
            }
        }

        // Determine if comeback (previous checkin was missed)
        $isComeback = self::isComeback($goal['id']);

        // Post public win message
        if ($channelId) {
            $event = $isComeback ? 'comeback' : 'win';
            $msg   = Library::get($goal['personality'], $event, $vars);
            Api::sendMessage($channelId, ['content' => $msg]);

            // Check overachiever: hit target this cycle?
            if ($isOverachiever) {
                $overMsg = Library::get($goal['personality'], 'overachiever', $vars);
                Api::sendMessage($channelId, [
                    'content'    => $overMsg,
                    'components' => [[
                        'type'       => Types::COMPONENT_ACTION_ROW,
                        'components' => [
                            [
                                'type'      => Types::COMPONENT_BUTTON,
                                'style'     => Types::BUTTON_PRIMARY,
                                'label'     => '🔄 Start new cycle now',
                                'custom_id' => "early_new_cycle:{$goal['id']}",
                            ],
                            [
                                'type'      => Types::COMPONENT_BUTTON,
                                'style'     => Types::BUTTON_SECONDARY,
                                'label'     => '⏳ Finish out this cycle',
                                'custom_id' => "early_finish_out:{$goal['id']}",
                            ],
                        ],
                    ]],
                ]);
            }
        }

        // Check milestones (after streak update happens in CycleEvaluator for cadenced goals;
        // for daily goals, update streak immediately)
        if ($goal['cadence_type'] === Types::CADENCE_DAILY) {
            $newStreak = (int)$goal['streak_count'] + 1;
            $newBest   = max($newStreak, (int)$goal['streak_best']);
            Database::execute(
                "UPDATE goals SET streak_count = :s, streak_best = :b WHERE id = :id",
                [':s' => $newStreak, ':b' => $newBest, ':id' => $goal['id']]
            );
            self::checkMilestone($goal, $newStreak, $channelId, $displayName);
        }

        return self::ephemeral("Logged! Great work on **{$goal['name']}**.");
    }

    private static function handleNotYet(array $goal, ?string $channelId, string $displayName): array
    {
        // Mark escalation pending — EscalationRunner will fire in 4h
        Database::execute(
            "UPDATE checkins SET escalation_level = 0 WHERE goal_id = :gid AND status = 'pending' ORDER BY scheduled_at DESC LIMIT 1",
            [':gid' => $goal['id']]
        );

        if ($channelId) {
            Api::sendMessage($channelId, [
                'content' => "{$displayName} says they'll do it later. We'll check back in 4 hours.",
            ]);
        }

        return self::ephemeral("Noted! We'll follow up in 4 hours.");
    }

    private static function handleSkipping(array $goal, ?string $channelId, array $vars): array
    {
        // Mark as skipped = miss
        Database::execute(
            "UPDATE checkins SET status = 'skipped', responded_at = NOW() WHERE goal_id = :gid AND status = 'pending' ORDER BY scheduled_at DESC LIMIT 1",
            [':gid' => $goal['id']]
        );

        // Break streak
        Database::execute(
            "UPDATE goals SET streak_count = 0 WHERE id = :id",
            [':id' => $goal['id']]
        );

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'miss', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return self::ephemeral("Skip recorded.");
    }

    private static function isComeback(string $goalId): bool
    {
        $last = Database::fetch(
            "SELECT status FROM checkins WHERE goal_id = :gid AND status NOT IN ('pending') ORDER BY scheduled_at DESC LIMIT 1",
            [':gid' => $goalId]
        );
        return $last && in_array($last['status'], ['missed', 'skipped'], true);
    }

    public static function checkMilestone(array $goal, int $streak, ?string $channelId, string $displayName): void
    {
        if (!$channelId || $streak === 0) return;

        // Convert streak (cycles) to days based on cadence
        $days = match ($goal['cadence_type']) {
            Types::CADENCE_DAILY        => $streak,
            Types::CADENCE_WEEKLY_ONCE,
            Types::CADENCE_WEEKLY_X     => $streak * 7,
            Types::CADENCE_MONTHLY_ONCE,
            Types::CADENCE_MONTHLY_X    => $streak * 30,
            default                     => $streak,
        };

        if (in_array($days, Types::MILESTONES, true)) {
            $label = match ($days) {
                7   => '1 week',
                14  => '2 weeks',
                30  => '1 month',
                90  => '3 months',
                180 => '6 months',
                365 => '1 year',
                default => "{$days} days",
            };
            $vars = ['name' => $displayName, 'goal' => $goal['name']];
            $msg  = Library::get($goal['personality'], 'milestone', $vars);
            Api::sendMessage($channelId, ['content' => "🏆 **{$label} milestone for {$displayName}!**\n{$msg}"]);
        }
    }

    private static function upsertMember(string $userId, string $guildId, string $displayName, array $interaction): void
    {
        $username = $interaction['member']['user']['username'] ?? $interaction['user']['username'] ?? $displayName;
        Database::execute(
            "INSERT INTO users (id, discord_username) VALUES (:id, :un)
             ON CONFLICT (id) DO UPDATE SET discord_username = EXCLUDED.discord_username",
            [':id' => $userId, ':un' => $username]
        );
        Database::execute(
            "INSERT INTO guild_members (guild_id, user_id, display_name, updated_at)
             VALUES (:gid, :uid, :dn, NOW())
             ON CONFLICT (guild_id, user_id) DO UPDATE SET display_name = EXCLUDED.display_name, updated_at = NOW()",
            [':gid' => $guildId, ':uid' => $userId, ':dn' => $displayName]
        );
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }
}
