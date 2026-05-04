<?php
namespace App;

/**
 * Database operations on the `users` table.
 * Phase 1: only basic email/password/role.
 */
class UserRepository
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, email, password_hash, role FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, email, role FROM users WHERE id = ? LIMIT 1'
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
}
