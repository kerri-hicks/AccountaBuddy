<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Buttons;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;
use AccountaBuddy\Messages\Library;

class Appeal
{
    public static function handle(array $interaction, string $appealId): array
    {
        $voterId = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $appeal = Database::fetch("SELECT * FROM streak_appeals WHERE id = :id", [':id' => $appealId]);
        if (!$appeal) {
            return self::ephemeral("Appeal not found.");
        }

        if ($appeal['status'] !== Types::APPEAL_OPEN) {
            return self::ephemeral("This appeal has already been resolved.");
        }

        if (strtotime($appeal['expires_at']) < time()) {
            return self::ephemeral("This appeal has expired.");
        }

        // Goal owner can't vote on their own appeal
        if ($appeal['user_id'] === $voterId) {
            return self::ephemeral("You can't vote on your own appeal.");
        }

        // Check duplicate vote
        $existing = Database::fetch(
            "SELECT id FROM appeal_votes WHERE appeal_id = :aid AND voter_user_id = :vid",
            [':aid' => $appealId, ':vid' => $voterId]
        );
        if ($existing) {
            return self::ephemeral("You've already voted on this appeal.");
        }

        // Cast vote
        Database::insert('appeal_votes', [
            'appeal_id'     => $appealId,
            'voter_user_id' => $voterId,
        ]);

        // Count votes
        $voteCount = (int)(Database::fetch(
            "SELECT COUNT(*) AS cnt FROM appeal_votes WHERE appeal_id = :id",
            [':id' => $appealId]
        )['cnt'] ?? 0);

        $config  = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        $channelId = $config['accountability_channel_id'] ?? null;

        if ($voteCount >= 5) {
            // Reinstate streak
            $goal = Database::fetch("SELECT * FROM goals WHERE id = :id", [':id' => $appeal['goal_id']]);
            if ($goal) {
                Database::execute(
                    "UPDATE goals SET streak_count = streak_best WHERE id = :id",
                    [':id' => $goal['id']]
                );
                Database::execute(
                    "UPDATE streak_appeals SET status = 'approved' WHERE id = :id",
                    [':id' => $appealId]
                );

                if ($channelId) {
                    $owner = Database::fetch(
                        "SELECT display_name FROM guild_members WHERE guild_id = :gid AND user_id = :uid",
                        [':gid' => $guildId, ':uid' => $appeal['user_id']]
                    );
                    $name = $owner['display_name'] ?? 'User';
                    $msg  = Library::get($goal['personality'], 'comeback', ['name' => $name, 'goal' => $goal['name']]);

                    $personalityIcon = match ($goal['personality']) {
                        Types::PERSONALITY_HYPE      => '🔥📣',
                        Types::PERSONALITY_DRY       => '📈📊',
                        Types::PERSONALITY_SARCASTIC => '👀🦊',
                        Types::PERSONALITY_HARSH     => '🗿💀',
                        default                      => '',
                    };
                    $header = Library::milesHeader($personalityIcon) . "\n***{$goal['name']}***";
                    $body = Library::getMessageOnly($goal['personality'], 'comeback', ['name' => $name, 'goal' => $goal['name']]);

                    Api::sendMessage($channelId, [
                        'content' => $header . "\n"
                                   . "✅ **Streak appeal approved!** {$voteCount} votes — {$name}'s streak has been reinstated.\n"
                                   . $body,
                    ]);
                }
            }

            return self::updateAndEphemeral($interaction, "Vote cast! The appeal passed — streak reinstated!");
        }

        return self::ephemeral("Vote cast! {$voteCount}/5 votes so far.");
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
