<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Buttons;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class Hold
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
        $vars        = ['name' => $displayName, 'goal' => $goal['name']];

        return match ($action) {
            'hold_keep'   => self::handleKeep($goal, $channelId, $vars),
            'hold_cancel' => self::handleCancel($goal, $channelId, $vars),
            default       => self::ephemeral("Unknown action."),
        };
    }

    private static function handleKeep(array $goal, ?string $channelId, array $vars): array
    {
        Database::execute(
            "UPDATE goals SET status = 'active' WHERE id = :id",
            [':id' => $goal['id']]
        );

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'comeback', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return self::ephemeral("Goal back in action! Check-ins will resume.");
    }

    private static function handleCancel(array $goal, ?string $channelId, array $vars): array
    {
        Database::execute(
            "UPDATE goals SET status = 'cancelled', cancelled_at = NOW() WHERE id = :id",
            [':id' => $goal['id']]
        );

        if ($channelId) {
            $msg = Library::get($goal['personality'], 'cancel', $vars);
            Api::sendMessage($channelId, ['content' => $msg]);
        }

        return self::ephemeral("Goal cancelled.");
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }
}
