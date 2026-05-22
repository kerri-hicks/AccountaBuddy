<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Buttons;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class OneTime
{
    public static function handle(array $interaction, string $action, string $goalId): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $goal = Database::fetch("SELECT * FROM goals WHERE id = :id", [':id' => $goalId]);
        if (!$goal || $goal['user_id'] !== $userId) {
            return self::ephemeral("These buttons aren't for you.");
        }

        $config      = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        $channelId   = $config['accountability_channel_id'] ?? null;
        $displayName = Api::resolveDisplayName($interaction);
        $dayCount    = (int)$goal['reminder_count'];
        $vars        = ['name' => $displayName, 'goal' => $goal['name'], 'N' => $dayCount];

        $msg = match ($action) {
            'one_time_did_it' => self::handleDone($goal, $channelId, $vars),
            'one_time_cancel' => self::handleCancel($goal, $channelId, $vars),
            default           => null,
        };

        if ($msg === null) {
            return self::ephemeral("Unknown action.");
        }

        return self::updateAndEphemeral($interaction, $msg);
    }

    private static function handleDone(array $goal, ?string $channelId, array $vars): string
    {
        Database::execute(
            "UPDATE goals SET status = 'completed', completed_at = NOW() WHERE id = :id",
            [':id' => $goal['id']]
        );

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'one_time_completion', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return "Marked as done! Goal slot freed.";
    }

    private static function handleCancel(array $goal, ?string $channelId, array $vars): string
    {
        Database::execute(
            "UPDATE goals SET status = 'cancelled', cancelled_at = NOW() WHERE id = :id",
            [':id' => $goal['id']]
        );

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'cancel', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return "Goal cancelled.";
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }

    private static function updateAndEphemeral(array $interaction, string $content): array
    {
        $appId = $interaction['application_id'] ?? '';
        $token = $interaction['token'] ?? '';
        if ($appId && $token && $content !== '') {
            try {
                Api::followUp($appId, $token, [
                    'content' => $content,
                    'flags'   => Types::FLAG_EPHEMERAL,
                ]);
            } catch (\Throwable $e) {
                error_log("Failed to send ephemeral follow-up: " . $e->getMessage());
            }
        }

        return [
            'type' => Types::UPDATE_MESSAGE,
            'data' => [
                'content'    => $interaction['message']['content'] ?? '',
                'embeds'     => $interaction['message']['embeds'] ?? [],
                'components' => [],
            ],
        ];
    }
}
