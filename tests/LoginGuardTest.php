<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';
include_once __DIR__ . '/stubs/ConstantStubs.php';
require_once __DIR__ . '/../libs/TapoLib.php';

/**
 * Prüft, dass das Modul ein Gerät nach abgelehnter Anmeldung nicht durch Wiederholungen sperrt.
 */
class LoginGuardTest extends TestCase
{
    public function testFirstRejectionPausesThenRetries(): void
    {
        $this->assertSame(\TpLink\LoginGuard::PauseSeconds, \TpLink\LoginGuard::GetPause(1));
        $this->assertSame(\TpLink\LoginGuard::PauseSeconds, \TpLink\LoginGuard::GetPause(1, 10));
    }

    public function testSecondRejectionStopsAutomaticLogin(): void
    {
        $this->assertNull(\TpLink\LoginGuard::GetPause(2));
        $this->assertNull(\TpLink\LoginGuard::GetPause(5));
    }

    public function testFewAttemptsLeftOnDeviceStopsImmediately(): void
    {
        $this->assertNull(\TpLink\LoginGuard::GetPause(1, 1));
        $this->assertNull(\TpLink\LoginGuard::GetPause(1, 0));
        $this->assertNull(\TpLink\LoginGuard::GetPause(1, \TpLink\LoginGuard::MinAttemptsLeft - 1));
        $this->assertSame(\TpLink\LoginGuard::PauseSeconds, \TpLink\LoginGuard::GetPause(1, \TpLink\LoginGuard::MinAttemptsLeft));
    }

    public function testAttemptsLeftFromDeviceResponse(): void
    {
        // Antwort einer TC71 nach einem Firmware-Update (anonymisiert, gekürzt)
        $Response = ['result' => ['data' => ['code' => -40401, 'time' => 9, 'max_time' => 10, 'encrypt_type' => ['3']]], 'error_code' => -40413];
        $this->assertSame(1, \TpLink\LoginGuard::GetAttemptsLeft($Response));
        $Response['result']['data']['time'] = 0;
        $this->assertSame(10, \TpLink\LoginGuard::GetAttemptsLeft($Response));
        $Response['result']['data']['time'] = 12;
        $this->assertSame(0, \TpLink\LoginGuard::GetAttemptsLeft($Response));
    }

    public function testUnknownAttemptsLeft(): void
    {
        $this->assertSame(-1, \TpLink\LoginGuard::GetAttemptsLeft(null));
        $this->assertSame(-1, \TpLink\LoginGuard::GetAttemptsLeft([]));
        $this->assertSame(-1, \TpLink\LoginGuard::GetAttemptsLeft(['result' => ['data' => ['nonce' => 'x']]]));
        $this->assertSame(-1, \TpLink\LoginGuard::GetAttemptsLeft(['result' => ['data' => ['time' => 'a', 'max_time' => 'b']]]));
    }

    public function testTranslationsExist(): void
    {
        $Locale = json_decode(file_get_contents(__DIR__ . '/../libs/locale.json'), true)['translations']['de'];
        foreach (['Login rejected', 'Login rejected. Next attempt in %d seconds.', 'Login rejected. Automatic login stopped, please check username and password and apply the configuration.'] as $Key) {
            $this->assertArrayHasKey($Key, $Locale);
        }
        $Form = json_decode(file_get_contents(__DIR__ . '/../libs/form.json'), true);
        $this->assertContains(202, array_column($Form['status'], 'code'));
    }
}
