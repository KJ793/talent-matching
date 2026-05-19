<?php
namespace App;

/**
 * Rule-based recommendation engine.
 * Phase 1: works only with the basic profile fields available so far.
 *   - Field-of-study/required-skills overlap
 *   - Years of experience proximity
 *   - Education level match
 * Always returns at most 10 results (TOP_K = 10) per the original spec.
 *
 * In Phase 2 this will be extended to consider skills, work mode preference
 * and preferred location, and the cap will become membership-aware.
 */
class Recommender
{
    public const TOP_K = 10;

    /**
     * Recommend jobs for a candidate.
     */
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
        return array_slice($scored, 0, self::TOP_K);
    }

    /**
     * Recommend candidates for an employer's job postings.
     */
    public static function candidatesForEmployer(int $employerUserId): array
    {
        $jobs = JobRepository::listByEmployer($employerUserId);
        if (empty($jobs)) {
            return [];
        }

        $candidates = CandidateRepository::listAll();
        $scored = [];
        foreach ($candidates as $candidate) {
            // Score against the best-matching job posting.
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
        return array_slice($scored, 0, self::TOP_K);
    }

    /**
     * Internal scoring function — sums weighted partial scores.
     */
    protected static function scoreJobForCandidate(array $job, array $candidate): int
    {
        $score = 0;

        // 1. Field-of-study vs required_skills / job description (token overlap, +5 per match)
        $candidateField = strtolower($candidate['field_of_study'] ?? '');
        $haystack = strtolower(($job['required_skills'] ?? '') . ' ' . ($job['description'] ?? ''));
        if ($candidateField && str_contains($haystack, $candidateField)) {
            $score += 10;
        }

        // 2. Education match (+5)
        if (!empty($candidate['education']) && !empty($job['required_education'])) {
            if (strcasecmp($candidate['education'], $job['required_education']) === 0) {
                $score += 5;
            }
        }

        // 3. Years of experience proximity
        $candYears = (int)($candidate['years_experience'] ?? 0);
        $jobYears  = (int)($job['years_experience'] ?? 0);
        $diff = abs($candYears - $jobYears);
        if ($diff === 0) $score += 8;
        elseif ($diff <= 2) $score += 4;
        elseif ($diff <= 4) $score += 1;

        return $score;
    }
}
