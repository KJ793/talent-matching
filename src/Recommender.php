<?php
namespace App;

/**
 * Rule-based recommendation engine.
 *
 * Phase 2: scoring extended to use the new candidate profile fields
 * (skills, preferred work mode, preferred location). The hard cap of 10
 * still applies to free users, but premium users see all matches.
 */
class Recommender
{
    public const TOP_K = 10;

    public static function jobsForCandidate(int $candidateUserId): array
    {
        $candidate = CandidateRepository::findByUserId($candidateUserId);
        if (!$candidate) {
            return [];
        }

        $jobs = JobRepository::listAll();
        $scored = [];
        foreach ($jobs as $job) {
            $score = self::scoreJobForCandidate($job, $candidate);
            if ($score > 0) {
                $scored[] = ['job' => $job, 'score' => $score];
            }
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        return UserRepository::isPremium($candidateUserId)
            ? $scored
            : array_slice($scored, 0, self::TOP_K);
    }

    public static function candidatesForEmployer(int $employerUserId): array
    {
        $jobs = JobRepository::listByEmployer($employerUserId);
        if (empty($jobs)) {
            return [];
        }

        $candidates = CandidateRepository::listAll();
        $scored = [];
        foreach ($candidates as $candidate) {
            $best = 0;
            foreach ($jobs as $job) {
                $s = self::scoreJobForCandidate($job, $candidate);
                if ($s > $best) $best = $s;
            }
            if ($best > 0) {
                $scored[] = ['candidate' => $candidate, 'score' => $best];
            }
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        return UserRepository::isPremium($employerUserId)
            ? $scored
            : array_slice($scored, 0, self::TOP_K);
    }

    /**
     * Scoring extended in Phase 2 to use skills, preferred work mode and location.
     */
    public static function scoreJobForCandidate(array $job, array $candidate): int
    {
        $score = 0;

        // 1. Skills overlap (most important — up to 20 points)
        $candidateSkills = self::tokenize($candidate['skills'] ?? '');
        $requiredSkills  = self::tokenize($job['required_skills'] ?? '');
        if (!empty($candidateSkills) && !empty($requiredSkills)) {
            $overlap = array_intersect($candidateSkills, $requiredSkills);
            $score += min(20, count($overlap) * 5);
        }

        // 2. Field-of-study mentioned in description / required skills
        $candidateField = strtolower($candidate['field_of_study'] ?? '');
        $haystack = strtolower(($job['required_skills'] ?? '') . ' ' . ($job['description'] ?? ''));
        if ($candidateField && str_contains($haystack, $candidateField)) {
            $score += 10;
        }

        // 3. Education match
        if (!empty($candidate['education']) && !empty($job['required_education'])) {
            if (strcasecmp($candidate['education'], $job['required_education']) === 0) {
                $score += 5;
            }
        }

        // 4. Years of experience proximity
        $candYears = (int)($candidate['years_experience'] ?? 0);
        $jobYears  = (int)($job['years_experience'] ?? 0);
        $diff = abs($candYears - $jobYears);
        if      ($diff === 0) $score += 8;
        elseif  ($diff <= 2)  $score += 4;
        elseif  ($diff <= 4)  $score += 1;

        // 5. Preferred work mode
        $pref = $candidate['preferred_work_mode'] ?? 'Any';
        if ($pref !== 'Any' && strcasecmp($pref, $job['work_mode'] ?? '') === 0) {
            $score += 6;
        }

        // 6. Preferred location
        if (!empty($candidate['preferred_location']) && !empty($job['location'])) {
            if (strcasecmp($candidate['preferred_location'], $job['location']) === 0) {
                $score += 6;
            }
        }

        return $score;
    }

    /**
     * Lowercase, trim, split on commas / semicolons / whitespace.
     */
    protected static function tokenize(string $text): array
    {
        $parts = preg_split('/[\s,;]+/', strtolower(trim($text))) ?: [];
        return array_values(array_filter($parts, fn($p) => $p !== ''));
    }
}
