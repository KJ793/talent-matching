<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Smoke tests for the password handling logic used by Auth::register() and Auth::attempt().
 * These don't touch the database — they verify that PHP's password_hash / password_verify
 * round-trip works as we depend on it doing.
 */
class AuthTest extends TestCase
{
    public function testPasswordRoundTrip(): void
    {
        $hash = password_hash('correct horse battery staple', PASSWORD_DEFAULT);
        $this->assertTrue(password_verify('correct horse battery staple', $hash));
        $this->assertFalse(password_verify('wrong password', $hash));
    }

    public function testHashesAreUnique(): void
    {
        // Same password should produce different hashes (different salts).
        $a = password_hash('hunter2', PASSWORD_DEFAULT);
        $b = password_hash('hunter2', PASSWORD_DEFAULT);
        $this->assertNotSame($a, $b);
        $this->assertTrue(password_verify('hunter2', $a));
        $this->assertTrue(password_verify('hunter2', $b));
    }
}
