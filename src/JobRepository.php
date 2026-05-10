<?php
namespace App;

/**
 * Job postings data access.
 * Phase 1: basic CRUD only. Search comes in commit 7, filters/fuzzy in commit 13.
 */
class JobRepository
{
    public static function create(int $employerId, array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO jobs (employer_id, title, description, required_education,
                               required_skills, years_experience, work_mode, location)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $employerId,
            $data['title']             ?? '',
            $data['description']       ?? '',
            $data['required_education']?? '',
            $data['required_skills']   ?? '',
            (int)($data['years_experience'] ?? 0),
            $data['work_mode']         ?? 'On-site',
            $data['location']          ?? '',
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $jobId, int $employerId, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE jobs SET title = ?, description = ?, required_education = ?,
                              required_skills = ?, years_experience = ?, work_mode = ?, location = ?
             WHERE id = ? AND employer_id = ?'
        );
        $stmt->execute([
            $data['title']             ?? '',
            $data['description']       ?? '',
            $data['required_education']?? '',
            $data['required_skills']   ?? '',
            (int)($data['years_experience'] ?? 0),
            $data['work_mode']         ?? 'On-site',
            $data['location']          ?? '',
            $jobId,
            $employerId,
        ]);
    }

    public static function delete(int $jobId, int $employerId): void
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM jobs WHERE id = ? AND employer_id = ?'
        );
        $stmt->execute([$jobId, $employerId]);
    }

    public static function find(int $jobId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT j.*, e.company_name
             FROM jobs j
             LEFT JOIN employers e ON e.user_id = j.employer_id
             WHERE j.id = ? LIMIT 1'
        );
        $stmt->execute([$jobId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function listByEmployer(int $employerId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll();
    }

    public static function listAll(): array
    {
        $stmt = Database::connection()->query(
            'SELECT j.*, e.company_name
             FROM jobs j LEFT JOIN employers e ON e.user_id = j.employer_id
             ORDER BY j.created_at DESC'
        );
        return $stmt->fetchAll();
    }
}
