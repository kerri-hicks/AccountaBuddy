<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Types;

class Timezone
{
    public static function handle(array $interaction): array
    {
        $userId   = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $username = $interaction['member']['user']['username'] ?? $interaction['user']['username'] ?? 'unknown';
        $tz       = trim($interaction['data']['options'][0]['options'][0]['value'] ?? '');

        if ($tz === '') {
            return self::ephemeral("Please provide a timezone name. Example: `/timezone America/New_York`");
        }

        try {
            new \DateTimeZone($tz);
        } catch (\Exception) {
            return self::ephemeral(
                "**\"{$tz}\"** isn't a valid timezone.\n" .
                "Use a TZ database name like `America/New_York`, `Europe/London`, or `Asia/Tokyo`.\n" .
                "Full list: <https://en.wikipedia.org/wiki/List_of_tz_database_time_zones>"
            );
        }

        Database::execute(
            "INSERT INTO users (id, discord_username, timezone, timezone_set)
             VALUES (:id, :un, :tz, TRUE)
             ON CONFLICT (id) DO UPDATE SET timezone = EXCLUDED.timezone, timezone_set = TRUE",
            [':id' => $userId, ':un' => $username, ':tz' => $tz]
        );

        $now = new \DateTime('now', new \DateTimeZone($tz));
        $localTime = $now->format('g:i A');

        return self::ephemeral(
            "✅ Your timezone is set to **{$tz}**.\n" .
            "Your current local time: **{$localTime}**.\n" .
            "Check-ins will now be scheduled against your local time."
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
