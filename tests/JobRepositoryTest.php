<?php
namespace Tests;

use App\Fuzzy;
use PHPUnit\Framework\TestCase;

/**
 * The JobRepository::search() method has two layers:
 *   1. SQL filtering (location, work_mode, min_experience) — covered by integration tests.
 *   2. In-PHP keyword filtering using Fuzzy::matches() — covered here by simulating
 *      the post-SQL rows and applying the same logic.
 */
class JobRepositoryTest extends TestCase
{
    private array $rows;

    protected function setUp(): void
    {
        $this->rows = [
            [
                'title' => 'Junior Software Engineer',
                'description' => 'Build PHP apps',
                'required_skills' => 'PHP, SQL',
                'location' => 'Sydney',
                'work_mode' => 'Hybrid',
                'company_name' => 'Acme',
                'required_education' => 'Bachelor',
            ],
            [
                'title' => 'Marketing Coordinator',
                'description' => 'Run ad campaigns',
                'required_skills' => 'SEO, Google Ads',
                'location' => 'Melbourne',
                'work_mode' => 'On-site',
                'company_name' => 'Globex',
                'required_education' => 'Bachelor',
            ],
        ];
    }

    public function testFuzzyKeywordPicksOnlyRelevantRow(): void
    {
        $matched = array_values(array_filter($this->rows, function ($row) {
            $hay = strtolower(implode(' ', [
                $row['title'], $row['description'], $row['required_skills'],
                $row['required_education'], $row['location'], $row['work_mode'],
                $row['company_name'],
            ]));
            return Fuzzy::matches($hay, 'sofware enginer');
        }));

        $this->assertCount(1, $matched);
        $this->assertSame('Junior Software Engineer', $matched[0]['title']);
    }

    public function testKeywordWithNoMatchesReturnsEmpty(): void
    {
        $matched = array_values(array_filter($this->rows, function ($row) {
            $hay = strtolower(implode(' ', [$row['title'], $row['description']]));
            return Fuzzy::matches($hay, 'astronaut');
        }));
        $this->assertSame([], $matched);
    }
}
