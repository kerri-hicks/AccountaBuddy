<?php

declare(strict_types=1);

namespace AccountaBuddy\Discord;

use AccountaBuddy\Config;

class Api
{
    private const BASE = 'https://discord.com/api/v10';

    private static function request(string $method, string $path, array $body = []): array
    {
        $url = self::BASE . $path;
        $json = json_encode($body, JSON_UNESCAPED_UNICODE);

        $ctx = stream_context_create([
            'http' => [
                'method'  => $method,
                'header'  => implode("\r\n", [
                    'Content-Type: application/json',
                    'Authorization: Bot ' . Config::botToken(),
                    'User-Agent: AccountaBuddy/1.0',
                ]),
                'content'         => $method !== 'GET' ? $json : null,
                'ignore_errors'   => true,
                'timeout'         => 10,
            ],
        ]);

        $raw = file_get_contents($url, false, $ctx);
        if ($raw === false) {
            throw new \RuntimeException("Discord API request failed: {$method} {$path}");
        }

        $data = json_decode($raw, true) ?? [];

        // Check HTTP status from $http_response_header
        $statusLine = $http_response_header[0] ?? '';
        preg_match('/HTTP\/\S+\s+(\d+)/', $statusLine, $m);
        $status = (int)($m[1] ?? 0);

        if ($status >= 400) {
            $msg = $data['message'] ?? $raw;
            throw new \RuntimeException("Discord API error {$status}: {$msg} ({$method} {$path})");
        }

        return $data;
    }

    public static function get(string $path): array
    {
        return self::request('GET', $path);
    }

    public static function post(string $path, array $body): array
    {
        return self::request('POST', $path, $body);
    }

    public static function patch(string $path, array $body): array
    {
        return self::request('PATCH', $path, $body);
    }

    public static function delete(string $path): array
    {
        return self::request('DELETE', $path);
    }

    // --- Convenience methods ---

    public static function sendMessage(string $channelId, array $data): array
    {
        return self::post("/channels/{$channelId}/messages", $data);
    }

    public static function sendEphemeral(string $channelId, string $content): array
    {
        return self::sendMessage($channelId, [
            'content' => $content,
            'flags'   => Types::FLAG_EPHEMERAL,
        ]);
    }

    public static function followUp(string $appId, string $token, array $data): array
    {
        return self::post("/webhooks/{$appId}/{$token}", $data);
    }

    public static function editFollowUp(string $appId, string $token, string $messageId, array $data): array
    {
        return self::patch("/webhooks/{$appId}/{$token}/messages/{$messageId}", $data);
    }

    public static function createDmChannel(string $userId): string
    {
        $data = self::post('/users/@me/channels', ['recipient_id' => $userId]);
        return $data['id'];
    }

    public static function sendDm(string $userId, array $messageData): array
    {
        $channelId = self::createDmChannel($userId);
        return self::sendMessage($channelId, $messageData);
    }

    public static function getGuildMember(string $guildId, string $userId): array
    {
        return self::get("/guilds/{$guildId}/members/{$userId}");
    }

    public static function resolveDisplayName(array $interaction): string
    {
        $member = $interaction['member'] ?? null;
        if ($member) {
            return $member['nick'] ?? $member['user']['username'] ?? 'unknown';
        }
        return $interaction['user']['username'] ?? 'unknown';
    }
}
