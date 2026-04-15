<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Commands;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Types;

class Setup
{
    public static function handle(array $interaction): array
    {
        // Must have MANAGE_GUILD permission (bit 5 = 0x20)
        $perms = (int)($interaction['member']['permissions'] ?? 0);
        if (!($perms & 0x20)) {
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => [
                    'content' => "You need the **Manage Server** permission to run setup.",
                    'flags'   => Types::FLAG_EPHEMERAL,
                ],
            ];
        }

        $guildId = $interaction['guild_id'] ?? '';
        $options = $interaction['data']['options'][0]['options'] ?? [];

        $channelId = null;
        $timezone  = 'UTC';
        foreach ($options as $opt) {
            if ($opt['name'] === 'channel')  $channelId = (string)$opt['value'];
            if ($opt['name'] === 'timezone') $timezone  = (string)$opt['value'];
        }

        if (!$channelId) {
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => ['content' => "Please provide a channel.", 'flags' => Types::FLAG_EPHEMERAL],
            ];
        }

        // Validate timezone
        try {
            new \DateTimeZone($timezone);
        } catch (\Exception) {
            return [
                'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => ['content' => "Invalid timezone `{$timezone}`. Use a valid TZ database name (e.g. `America/New_York`).", 'flags' => Types::FLAG_EPHEMERAL],
            ];
        }

        Database::execute(
            "INSERT INTO server_config (guild_id, accountability_channel_id, timezone)
             VALUES (:gid, :cid, :tz)
             ON CONFLICT (guild_id) DO UPDATE SET accountability_channel_id = EXCLUDED.accountability_channel_id, timezone = EXCLUDED.timezone",
            [':gid' => $guildId, ':cid' => $channelId, ':tz' => $timezone]
        );

        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => [
                'content' => "✅ AccountaBuddy configured!\n- Accountability channel: <#{$channelId}>\n- Timezone: `{$timezone}`",
                'flags'   => Types::FLAG_EPHEMERAL,
            ],
        ];
    }
}
