<?php
namespace Tests;

use App\Fuzzy;
use PHPUnit\Framework\TestCase;

class FuzzyTest extends TestCase
{
    public function testExactMatch(): void
    {
        $this->assertTrue(Fuzzy::matches('software engineer', 'software engineer'));
    }

    public function testHandlesTypos(): void
    {
        // "sofware enginer" → should still match "software engineer"
        $this->assertTrue(Fuzzy::matches('software engineer wanted', 'sofware enginer'));
    }

    public function testSubstringMatch(): void
    {
        // "java" should be found inside "javascript"
        $this->assertTrue(Fuzzy::matches('javascript developer', 'java'));
    }

    public function testCompletelyDifferentTokensDoNotMatch(): void
    {
        $this->assertFalse(Fuzzy::matches('marketing manager', 'software engineer'));
    }

    public function testAllNeedleTokensMustHit(): void
    {
        // "data analyst" requires both "data" AND "analyst" to appear (fuzzily)
        $this->assertTrue(Fuzzy::matches('senior data analyst position', 'data analyst'));
        $this->assertFalse(Fuzzy::matches('senior accountant position', 'data analyst'));
    }

    public function testScoreOrdering(): void
    {
        $exact   = Fuzzy::score('software engineer', 'software engineer');
        $typo    = Fuzzy::score('software engineer', 'sofware enginer');
        $weak    = Fuzzy::score('software engineer', 'develop');
        $this->assertGreaterThan($typo, $exact);
        $this->assertGreaterThanOrEqual($weak, $typo);
    }

    public function testTokenize(): void
    {
        $this->assertSame(['php', 'sql'], Fuzzy::tokenize('PHP, SQL'));
        $this->assertSame(['php', 'sql'], Fuzzy::tokenize('php   sql'));
        $this->assertSame([], Fuzzy::tokenize('   '));
    }

    public function testEmptyNeedleAlwaysMatches(): void
    {
        $this->assertTrue(Fuzzy::matches('anything here', ''));
    }
}
