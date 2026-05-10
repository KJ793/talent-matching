<?php
namespace App;

class EmployerRepository
{
    public static function findByUserId(int $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM employers WHERE user_id = ? LIMIT 1'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function save(int $userId, array $data): void
    {
        $existing = self::findByUserId($userId);
        $payload = [
            'user_id'      => $userId,
            'company_name' => $data['company_name'] ?? '',
            'company_info' => $data['company_info'] ?? '',
        ];

        if ($existing) {
            $sql = 'UPDATE employers SET company_name = :company_name, company_info = :company_info WHERE user_id = :user_id';
        } else {
            $sql = 'INSERT INTO employers (user_id, company_name, company_info) VALUES (:user_id, :company_name, :company_info)';
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($payload);
    }
}
