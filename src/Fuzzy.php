<?php
namespace App;

/**
 * Token-oriented fuzzy matching used by the v2 candidate/job search.
 *
 * The needle is split into tokens; a result row is a match only when every
 * token finds a close token in the haystack (AND semantics). Closeness is
 * checked with exact equality, substring containment for tokens >= 3 chars,
 * a scaled Levenshtein bound, and a Soundex fallback for English words.
 */
class Fuzzy
{
    public static function tokenize(string $text): array
    {
        $lower = mb_strtolower($text);
        $parts = preg_split('/[\s,;\/]+/u', $lower) ?: [];
        return array_values(array_filter($parts, static fn ($t) => $t !== ''));
    }

    public static function tokensClose(string $a, string $b, int $maxDistance = 2): bool
    {
        if ($a === $b) {
            return true;
        }
        if (strlen($a) >= 3 && strlen($b) >= 3) {
            if (str_contains($a, $b) || str_contains($b, $a)) {
                return true;
            }
        }
        $minLen  = min(strlen($a), strlen($b));
        $allowed = min($maxDistance, max(1, (int) floor($minLen / 3)));
        if (levenshtein($a, $b) <= $allowed) {
            return true;
        }
        return soundex($a) === soundex($b);
    }

    /**
     * Returns true when every needle token has at least one close token
     * in the haystack. An empty needle matches anything.
     */
    public static function matches(string $haystack, string $needle, int $maxDistance = 2): bool
    {
        $needleTokens = self::tokenize($needle);
        if ($needleTokens === []) {
            return true;
        }
        $hayTokens = self::tokenize($haystack);
        if ($hayTokens === []) {
            return false;
        }
        foreach ($needleTokens as $nt) {
            $hit = false;
            foreach ($hayTokens as $ht) {
                if (self::tokensClose($nt, $ht, $maxDistance)) {
                    $hit = true;
                    break;
                }
            }
            if (!$hit) {
                return false;
            }
        }
        return true;
    }

    /**
     * Per-token relevance score. Sums across all needle tokens:
     *   exact match within haystack tokens .... 5
     *   Levenshtein distance <= 1 ............. 3
     *   Levenshtein distance <= 2 ............. 2
     *   Soundex match (English phonetic) ...... 1
     * Higher score = more relevant. Used to rank search results.
     */
    public static function score(string $haystack, string $needle): int
    {
        $needleTokens = self::tokenize($needle);
        if ($needleTokens === []) {
            return 0;
        }
        $hayTokens = self::tokenize($haystack);
        if ($hayTokens === []) {
            return 0;
        }
        $total = 0;
        foreach ($needleTokens as $nt) {
            $best = 0;
            foreach ($hayTokens as $ht) {
                $points = 0;
                if ($nt === $ht) {
                    $points = 5;
                } else {
                    $dist = levenshtein($nt, $ht);
                    if ($dist <= 1) {
                        $points = 3;
                    } elseif ($dist <= 2) {
                        $points = 2;
                    } elseif (soundex($nt) === soundex($ht)) {
                        $points = 1;
                    }
                }
                if ($points > $best) {
                    $best = $points;
                }
            }
            $total += $best;
        }
        return $total;
    }
}
