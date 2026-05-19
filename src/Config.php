<?php

declare(strict_types=1);

namespace AccountaBuddy;

class Config
{
    private static array $cache = [];

    public static function get(string $key): string
    {
        if (!isset(self::$cache[$key])) {
            $value = $_ENV[$key] ?? getenv($key);
            if ($value === false || $value === '') {
                throw new \RuntimeException("Missing required environment variable: {$key}");
            }
            self::$cache[$key] = (string) $value;
        }
        return self::$cache[$key];
    }

    public static function appId(): string     { return self::get('DISCORD_APP_ID'); }
    public static function botToken(): string  { return self::get('DISCORD_BOT_TOKEN'); }
    public static function publicKey(): string { return self::get('DISCORD_PUBLIC_KEY'); }
    public static function databaseUrl(): string { return self::get('DATABASE_URL'); }
}
