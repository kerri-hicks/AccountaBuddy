<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Modals;

use AccountaBuddy\Database;
use AccountaBuddy\Discord\Api;
use AccountaBuddy\Discord\Types;

class GoalCreate
{
    public static function handle(array $interaction): array
    {
        $userId  = $interaction['member']['user']['id'] ?? $interaction['user']['id'] ?? '';
        $guildId = $interaction['guild_id'] ?? '';

        $components = $interaction['data']['components'] ?? [];
        $fields = [];
        foreach ($components as $row) {
            foreach ($row['components'] as $comp) {
                $fields[$comp['custom_id']] = $comp['value'];
            }
        }

        $name        = trim($fields['goal_name'] ?? '');
        $description = trim($fields['goal_description'] ?? '') ?: null;
        $cadenceRaw  = strtolower(trim($fields['goal_cadence'] ?? ''));
        $timeRaw     = trim($fields['goal_checkin_time'] ?? '');
        $persRaw     = strtolower(trim($fields['goal_personality'] ?? ''));

        // Validate name
        if ($name === '') {
            return self::ephemeral("Goal name cannot be empty.");
        }

        // Parse personality
        $personality = self::parsePersonality($persRaw);
        if (!$personality) {
            return self::ephemeral("Invalid personality. Choose: `hype`, `dry`, `sarcastic`, or `harsh`.");
        }

        // Parse cadence
        [$cadenceType, $cadenceTarget] = self::parseCadence($cadenceRaw);
        if (!$cadenceType) {
            return self::ephemeral("Invalid cadence. Use: `daily`, `one-time`, `weekly-1` through `weekly-7`, or `monthly-1` through `monthly-30`.");
        }

        // Parse time (HH:MM)
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $timeRaw, $m)) {
            return self::ephemeral("Invalid check-in time. Use HH:MM format (24h UTC), e.g. `08:00`.");
        }
        $hour   = (int)$m[1];
        $minute = (int)$m[2];
        if ($hour > 23 || $minute > 59) {
            return self::ephemeral("Invalid check-in time. Hours 0–23, minutes 0–59.");
        }
        $checkinTime = sprintf('%02d:%02d:00', $hour, $minute);

        // Upsert user and guild_member
        $displayName = Api::resolveDisplayName($interaction);
        $username    = $interaction['member']['user']['username'] ?? $interaction['user']['username'] ?? $displayName;

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

        // Determine cycle start date (null for one-time)
        $cycleStartDate = $cadenceType === Types::CADENCE_ONE_TIME ? null : date('Y-m-d');

        $goalId = Database::insert('goals', [
            'user_id'          => $userId,
            'guild_id'         => $guildId,
            'name'             => $name,
            'description'      => $description,
            'personality'      => $personality,
            'cadence_type'     => $cadenceType,
            'cadence_target'   => $cadenceTarget,
            'checkin_time'     => $checkinTime,
            'cycle_start_date' => $cycleStartDate,
            'status'           => Types::GOAL_ACTIVE,
        ]);

        // Create initial cycle (not for one-time goals)
        if ($cadenceType !== Types::CADENCE_ONE_TIME) {
            $cycleEnd = self::cycleEndDate($cadenceType, date('Y-m-d'));
            Database::insert('cycles', [
                'goal_id'    => $goalId,
                'start_date' => date('Y-m-d'),
                'end_date'   => $cycleEnd,
                'target'     => $cadenceTarget,
                'completions' => 0,
                'status'     => Types::CYCLE_ACTIVE,
            ]);
        }

        // Post publicly in accountability channel
        $config = Database::fetch("SELECT * FROM server_config WHERE guild_id = :gid", [':gid' => $guildId]);
        if ($config) {
            $personalityLabel = match ($personality) {
                Types::PERSONALITY_HYPE      => '🔥📣 Hype Coach',
                Types::PERSONALITY_DRY       => '📈📊 Dry Colleague',
                Types::PERSONALITY_SARCASTIC => '👀🦊 Sarcastic Friend',
                Types::PERSONALITY_HARSH     => '🗿💀 Harsh Critic',
            };
            $cadenceLabel = \AccountaBuddy\Handlers\Commands\GoalList::formatCadence($cadenceType, $cadenceTarget);

            $lines = [
                "📋 **New goal from {$displayName}!**",
                "**{$name}**" . ($description ? "\n_{$description}_" : ''),
                "Cadence: {$cadenceLabel} | Personality: {$personalityLabel}",
                "Check-in time: {$checkinTime} UTC",
            ];
            if ($cadenceType !== Types::CADENCE_ONE_TIME) {
                $lines[] = "_Note: 'monthly' cycles are always 30 days, never a calendar month._";
            }

            Api::sendMessage($config['accountability_channel_id'], ['content' => implode("\n", $lines)]);
        }

        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => [
                'content' => "✅ Goal **{$name}** created! Your first check-in will fire at {$checkinTime} UTC.",
                'flags'   => Types::FLAG_EPHEMERAL,
            ],
        ];
    }

    private static function parsePersonality(string $raw): ?string
    {
        return match ($raw) {
            'hype', 'hype_coach', 'hypecoach'         => Types::PERSONALITY_HYPE,
            'dry', 'dry_colleague', 'drycolleague'     => Types::PERSONALITY_DRY,
            'sarcastic', 'sarcastic_friend'            => Types::PERSONALITY_SARCASTIC,
            'harsh', 'harsh_critic', 'harshcritic'     => Types::PERSONALITY_HARSH,
            default                                     => null,
        };
    }

    private static function parseCadence(string $raw): array
    {
        if ($raw === 'daily')    return [Types::CADENCE_DAILY, 1];
        if ($raw === 'one-time' || $raw === 'one_time' || $raw === 'onetime') return [Types::CADENCE_ONE_TIME, 1];

        if (preg_match('/^weekly-(\d+)$/', $raw, $m)) {
            $n = (int)$m[1];
            if ($n < 1 || $n > 7) return [null, 0];
            return $n === 1 ? [Types::CADENCE_WEEKLY_ONCE, 1] : [Types::CADENCE_WEEKLY_X, $n];
        }

        if (preg_match('/^monthly-(\d+)$/', $raw, $m)) {
            $n = (int)$m[1];
            if ($n < 1 || $n > 30) return [null, 0];
            return $n === 1 ? [Types::CADENCE_MONTHLY_ONCE, 1] : [Types::CADENCE_MONTHLY_X, $n];
        }

        return [null, 0];
    }

    public static function cycleEndDate(string $cadenceType, string $startDate): string
    {
        if (in_array($cadenceType, [Types::CADENCE_WEEKLY_X, Types::CADENCE_WEEKLY_ONCE], true)) {
            return date('Y-m-d', strtotime($startDate . ' +6 days'));
        }
        // monthly (30 days) — end = start + 29 days (inclusive)
        return date('Y-m-d', strtotime($startDate . ' +29 days'));
    }

    private static function ephemeral(string $content): array
    {
        return [
            'type' => Types::CHANNEL_MESSAGE_WITH_SOURCE,
            'data' => ['content' => $content, 'flags' => Types::FLAG_EPHEMERAL],
        ];
    }
}
