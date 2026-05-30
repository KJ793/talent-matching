<?php
namespace Tests;

use App\Recommender;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the pure scoring function. The DB-touching methods (jobsForCandidate /
 * candidatesForEmployer) are exercised through integration tests in CI when a
 * test DB is available; the scoring logic is the interesting part to unit-test.
 */
class RecommenderTest extends TestCase
{
    public function testStrongMatchScoresHighly(): void
    {
        $candidate = [
            'field_of_study' => 'Computer Science',
            'education'      => 'Bachelor',
            'years_experience' => 2,
            'skills'           => 'PHP, JavaScript, SQL',
            'preferred_work_mode' => 'Hybrid',
            'preferred_location'  => 'Sydney',
        ];
        $job = [
            'description'        => 'Building web apps in Computer Science domain',
            'required_education' => 'Bachelor',
            'years_experience'   => 2,
            'required_skills'    => 'PHP, JavaScript, SQL',
            'work_mode'          => 'Hybrid',
            'location'           => 'Sydney',
        ];
        $score = Recommender::scoreJobForCandidate($job, $candidate);
        // skills (15) + field (10) + edu (5) + years exact (8) + mode (6) + loc (6) = 50
        $this->assertGreaterThanOrEqual(40, $score);
    }

    public function testNoMatchScoresZero(): void
    {
        $candidate = [
            'field_of_study' => 'Marketing',
            'education'      => 'Diploma',
            'years_experience' => 0,
            'skills'           => 'Photoshop',
            'preferred_work_mode' => 'On-site',
            'preferred_location'  => 'Brisbane',
        ];
        $job = [
            'description'        => 'Cloud DevOps role',
            'required_education' => 'Master',
            'years_experience'   => 8,
            'required_skills'    => 'AWS, Kubernetes',
            'work_mode'          => 'Remote',
            'location'           => 'Sydney',
        ];
        $this->assertSame(0, Recommender::scoreJobForCandidate($job, $candidate));
    }

    public function testPartialSkillsOverlapStillScores(): void
    {
        $candidate = ['skills' => 'PHP, JavaScript', 'years_experience' => 0];
        $job = ['required_skills' => 'PHP, Python', 'years_experience' => 0];
        $score = Recommender::scoreJobForCandidate($job, $candidate);
        $this->assertGreaterThan(0, $score);
    }

    public function testYearsProximity(): void
    {
        $exact = Recommender::scoreJobForCandidate(
            ['years_experience' => 3, 'required_skills' => '', 'description' => '', 'work_mode' => '', 'location' => '', 'required_education' => ''],
            ['years_experience' => 3, 'skills' => '', 'field_of_study' => '', 'preferred_work_mode' => 'Any', 'preferred_location' => '', 'education' => '']
        );
        $close = Recommender::scoreJobForCandidate(
            ['years_experience' => 4, 'required_skills' => '', 'description' => '', 'work_mode' => '', 'location' => '', 'required_education' => ''],
            ['years_experience' => 3, 'skills' => '', 'field_of_study' => '', 'preferred_work_mode' => 'Any', 'preferred_location' => '', 'education' => '']
        );
        $far = Recommender::scoreJobForCandidate(
            ['years_experience' => 10, 'required_skills' => '', 'description' => '', 'work_mode' => '', 'location' => '', 'required_education' => ''],
            ['years_experience' => 0, 'skills' => '', 'field_of_study' => '', 'preferred_work_mode' => 'Any', 'preferred_location' => '', 'education' => '']
        );

        $this->assertGreaterThan($close, $exact);
        $this->assertGreaterThanOrEqual($far, $close);
    }
}
