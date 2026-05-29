<?php
namespace App;

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
            'SELECT j.*, e.company_name, e.company_info
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

    /**
     * Phase 1 keyword search: original spec only requires searching the
     * job description. Kept for backwards compatibility with older callers.
     */
    public static function searchByKeyword(string $keyword): array
    {
        $like = '%' . $keyword . '%';
        $stmt = Database::connection()->prepare(
            'SELECT j.*, e.company_name
             FROM jobs j LEFT JOIN employers e ON e.user_id = j.employer_id
             WHERE j.description LIKE ?
             ORDER BY j.created_at DESC'
        );
        $stmt->execute([$like]);
        return $stmt->fetchAll();
    }

    /**
     * Phase 2 search: structured filters in SQL, fuzzy keyword in PHP.
     *
     * $params keys (all optional):
     *   keyword            free-text, fuzzy-matched against title + description + required_skills
     *   work_mode          exact match on jobs.work_mode (Remote|On-site|Hybrid)
     *   location           exact match on jobs.location
     *   required_education exact match on jobs.required_education
     *   min_experience     jobs.years_experience <= this many (a candidate with N years
     *                      qualifies for postings requiring N or fewer)
     */
    public static function search(array $params): array
    {
        $sql = 'SELECT j.*, e.company_name
                FROM jobs j LEFT JOIN employers e ON e.user_id = j.employer_id
                WHERE 1 = 1';
        $args = [];

        if (!empty($params['work_mode'])) {
            $sql   .= ' AND j.work_mode = ?';
            $args[] = $params['work_mode'];
        }
        if (!empty($params['location'])) {
            $sql   .= ' AND j.location = ?';
            $args[] = $params['location'];
        }
        if (!empty($params['required_education'])) {
            $sql   .= ' AND j.required_education = ?';
            $args[] = $params['required_education'];
        }
        if (isset($params['min_experience']) && $params['min_experience'] !== '') {
            $sql   .= ' AND j.years_experience <= ?';
            $args[] = (int) $params['min_experience'];
        }
        $sql .= ' ORDER BY j.created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($args);
        $rows = $stmt->fetchAll();

        $keyword = trim((string) ($params['keyword'] ?? ''));
        if ($keyword === '') {
            return $rows;
        }

        $scored = [];
        foreach ($rows as $row) {
            $haystack = ($row['title'] ?? '') . ' '
                      . ($row['description'] ?? '') . ' '
                      . ($row['required_skills'] ?? '');
            if (!Fuzzy::matches($haystack, $keyword)) {
                continue;
            }
            $scored[] = ['row' => $row, 'score' => Fuzzy::score($haystack, $keyword)];
        }

        usort($scored, static fn ($a, $b) => $b['score'] <=> $a['score']);
        return array_map(static fn ($s) => $s['row'], $scored);
    }
}