<?php
namespace App;

class CandidateRepository
{
    public static function findByUserId(int $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT c.*, u.email, u.membership FROM candidates c
             JOIN users u ON u.id = c.user_id
             WHERE c.user_id = ? LIMIT 1'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function save(int $userId, array $data): void
    {
        $existing = self::findByUserId($userId);

        $payload = [
            'user_id'             => $userId,
            'full_name'           => $data['full_name']        ?? '',
            'contact'             => $data['contact']          ?? '',
            'education'           => $data['education']        ?? '',
            'field_of_study'      => $data['field_of_study']   ?? '',
            'years_experience'    => (int)($data['years_experience'] ?? 0),
            'skills'              => $data['skills']              ?? '',
            'work_experience'     => $data['work_experience']     ?? '',
            'preferred_work_mode' => $data['preferred_work_mode'] ?? 'Any',
            'preferred_location'  => $data['preferred_location']  ?? '',
        ];

        if ($existing) {
            $sql = 'UPDATE candidates SET
                        full_name = :full_name,
                        contact = :contact,
                        education = :education,
                        field_of_study = :field_of_study,
                        years_experience = :years_experience,
                        skills = :skills,
                        work_experience = :work_experience,
                        preferred_work_mode = :preferred_work_mode,
                        preferred_location = :preferred_location
                    WHERE user_id = :user_id';
        } else {
            $sql = 'INSERT INTO candidates
                        (user_id, full_name, contact, education, field_of_study, years_experience,
                         skills, work_experience, preferred_work_mode, preferred_location)
                    VALUES
                        (:user_id, :full_name, :contact, :education, :field_of_study, :years_experience,
                         :skills, :work_experience, :preferred_work_mode, :preferred_location)';
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($payload);
    }

    public static function listAll(): array
    {
        $stmt = Database::connection()->query(
            'SELECT c.*, u.email FROM candidates c
             JOIN users u ON u.id = c.user_id
             ORDER BY c.full_name'
        );
        return $stmt->fetchAll();
    }

    public static function searchByKeyword(string $keyword): array
    {
        $like = '%' . $keyword . '%';
        $stmt = Database::connection()->prepare(
            'SELECT c.*, u.email FROM candidates c
             JOIN users u ON u.id = c.user_id
             WHERE c.full_name LIKE ?
                OR c.education LIKE ?
                OR c.field_of_study LIKE ?
                OR c.skills LIKE ?
                OR c.work_experience LIKE ?
                OR c.preferred_location LIKE ?
             ORDER BY c.full_name'
        );
        $stmt->execute([$like, $like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }
}
}