<?php

declare(strict_types=1);

namespace AccountaBuddy;

use PDO;
use PDOStatement;

class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $url = Config::databaseUrl();
            $parts = parse_url($url);

            $dsn = sprintf(
                'pgsql:host=%s;port=%d;dbname=%s',
                $parts['host'],
                $parts['port'] ?? 5432,
                ltrim($parts['path'], '/')
            );

            self::$pdo = new PDO(
                $dsn,
                $parts['user'] ?? null,
                $parts['pass'] ?? null,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row !== false ? $row : null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    public static function insert(string $table, array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($k) => ":{$k}", array_keys($data)));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders}) RETURNING id";
        $row = self::fetch($sql, $data);
        return (string) $row['id'];
    }

    public static function lastInsertId(): string
    {
        return self::connection()->lastInsertId();
    }
}
