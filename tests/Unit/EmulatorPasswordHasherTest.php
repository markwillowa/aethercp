<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\Emulator\EmulatorPasswordHasher;

class EmulatorPasswordHasherTest extends TestCase
{
    public function test_md5_hashing_and_verification(): void
    {
        $hasher = new EmulatorPasswordHasher('md5');
        $rawPass = 'Maki0913$';
        $hashed = $hasher->hash($rawPass);

        $this->assertSame(32, strlen($hashed));
        $this->assertSame(md5($rawPass), $hashed);
        $this->assertTrue($hasher->verify($rawPass, $hashed));
        $this->assertFalse($hasher->verify('WrongPassword!', $hashed));
    }

    public function test_bcrypt_hashing_and_verification(): void
    {
        $hasher = new EmulatorPasswordHasher('bcrypt');
        $rawPass = 'SuperSecret123!';
        $hashed = $hasher->hash($rawPass);

        $this->assertTrue(str_starts_with($hashed, '$2y$'));
        $this->assertTrue($hasher->verify($rawPass, $hashed));
        $this->assertFalse($hasher->verify('BadPassword!', $hashed));
    }

    public function test_sha256_hashing_and_verification(): void
    {
        $hasher = new EmulatorPasswordHasher('sha256');
        $rawPass = 'HerculesPass2026';
        $hashed = $hasher->hash($rawPass);

        $this->assertSame(64, strlen($hashed));
        $this->assertSame(hash('sha256', $rawPass), $hashed);
        $this->assertTrue($hasher->verify($rawPass, $hashed));
        $this->assertFalse($hasher->verify('WrongPass', $hashed));
    }

    public function test_auto_detection_of_mixed_hash_formats(): void
    {
        $hasher = new EmulatorPasswordHasher('bcrypt');

        // Legacy MD5 password from older player account
        $legacyMd5 = md5('MyOldRagnarokPass');
        $this->assertTrue($hasher->verify('MyOldRagnarokPass', $legacyMd5));

        // Hercules SHA256 password
        $herculesSha = hash('sha256', 'HerculesPass');
        $this->assertTrue($hasher->verify('HerculesPass', $herculesSha));

        // Modern Bcrypt
        $bcrypt = password_hash('ModernPass', PASSWORD_BCRYPT);
        $this->assertTrue($hasher->verify('ModernPass', $bcrypt));
    }
}

