<?php
namespace App;

class UserRepository
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, email, password_hash, role, membership FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, email, role, membership FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $email, string $password, string $role): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)'
        );
        $stmt->execute([$email, $hash, $role]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function getMembership(int $userId): string
    {
        $stmt = Database::connection()->prepare('SELECT membership FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $m = $stmt->fetchColumn();
        return $m ?: 'free';
    }

    public static function isPremium(int $userId): bool
    {
        return self::getMembership($userId) === 'premium';
    }

    public static function setMembership(int $userId, string $membership): void
    {
        if (!in_array($membership, ['free', 'premium'], true)) {
            throw new \InvalidArgumentException('Invalid membership tier.');
        }
        $stmt = Database::connection()->prepare('UPDATE users SET membership = ? WHERE id = ?');
        $stmt->execute([$membership, $userId]);
    }
}
