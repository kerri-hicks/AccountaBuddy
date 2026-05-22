<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Config;
use AccountaBuddy\Messages\Library;

class GoalAppeal
{
    public static function handle(array $interaction): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $subcommandOptions = [];
        foreach ($interaction['data']['options'] ?? [] as $opt) {
            if (($opt['type'] ?? 0) === 1 && ($opt['name'] ?? '') === 'appeal') {
                $subcommandOptions = $opt['options'] ?? [];
                break;
            }
        }
        $options = !empty($subcommandOptions) ? $subcommandOptions : ($interaction['data']['options'] ?? []);
        $goalArg = '';
        foreach ($options as $opt) {
            if ($opt['name'] === 'goal') { $goalArg = (string)$opt['value']; break; }
        }

        $goal = GoalView::findGoal($userId, $guildId, $goalArg);

        if (!$goal) {
            return self::ephemeral("Goal not found.");
        }

        if ($goal['cadence_type'] === Types::CADENCE_ONE_TIME) {
            return self::ephemeral("One-time goals don't have streaks, so there's nothing to appeal.");
        }

        // Check for open appeal already
        $existing = Database::fetch(
            "SELECT id FROM streak_appeals WHERE goal_id = :gid AND status = 'open'",
            [':gid' => $goal['id']]
        );
        if ($existing) {
            return self::ephemeral("There's already an open appeal for this goal. Check the channel for the vote.");
        }

        $displayName = Api::resolveDisplayName($interaction);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $appealId = Database::insert('streak_appeals', [
            'goal_id'    => $goal['id'],
            'user_id'    => $userId,
            'expires_at' => $expiresAt,
            'status'     => Types::APPEAL_OPEN,
        ]);

        $config = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        if (!$config) {
            return self::ephemeral("Server not configured. Ask an admin to run `/accountabuddy setup`.");
        }

        $personalityIcon = match ($goal['personality']) {
            Types::PERSONALITY_HYPE      => '🔥📣',
            Types::PERSONALITY_DRY       => '📈📊',
            Types::PERSONALITY_SARCASTIC => '👀🦊',
            Types::PERSONALITY_HARSH     => '🗿💀',
            default                      => '',
        };
        $header = Library::milesHeader($personalityIcon) . "\n***{$goal['name']}***";

        $msg = $header . "\n"
             . "**{$displayName}** is appealing a broken streak!\n"
             . "Vote to reinstate their streak. 5 votes needed within 24 hours.\n"
             . "*(Appeal expires: <t:" . strtotime($expiresAt) . ":R>)*";

        $posted = Api::sendMessage($config['accountability_channel_id'], [
            'content'    => $msg,
            'components' => [[
                'type'       => Types::COMPONENT_ACTION_ROW,
                'components' => [[
                    'type'      => Types::COMPONENT_BUTTON,
                    'style'     => Types::BUTTON_SUCCESS,
                    'label'     => '✅ Reinstate streak',
                    'custom_id' => "appeal_vote:{$appealId}",
                ]],
            ]],
        ]);

        // Store message ID for later editing
        Database::execute(
            "UPDATE streak_appeals SET discord_message_id = :mid WHERE id = :id",
            [':mid' => $posted['id'] ?? null, ':id' => $appealId]
        );

        return self::ephemeral("Appeal posted! The community has 24 hours to vote. 5 votes = streak reinstated.");
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }
}
