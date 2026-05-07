<?php
namespace App;

/**
 * Candidate profile data access.
 * Phase 1: basic fields only (full_name, contact, education, field_of_study, years_experience).
 * Skills, work experience and preferences are added in Phase 2.
 */
class CandidateRepository
{
    public static function findByUserId(int $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM candidates WHERE user_id = ? LIMIT 1'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function save(int $userId, array $data): void
    {
        $existing = self::findByUserId($userId);

        $payload = [
            'user_id'          => $userId,
            'full_name'        => $data['full_name']        ?? '',
            'contact'          => $data['contact']          ?? '',
            'education'        => $data['education']        ?? '',
            'field_of_study'   => $data['field_of_study']   ?? '',
            'years_experience' => (int)($data['years_experience'] ?? 0),
        ];

        if ($existing) {
            $sql = 'UPDATE candidates SET full_name = :full_name, contact = :contact,
                    education = :education, field_of_study = :field_of_study,
                    years_experience = :years_experience
                    WHERE user_id = :user_id';
        } else {
            $sql = 'INSERT INTO candidates (user_id, full_name, contact, education, field_of_study, years_experience)
                    VALUES (:user_id, :full_name, :contact, :education, :field_of_study, :years_experience)';
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($payload);
    }
}