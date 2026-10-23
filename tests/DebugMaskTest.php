<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';
include_once __DIR__ . '/stubs/ConstantStubs.php';
require_once __DIR__ . '/../libs/TapoLib.php';

/**
 * Prüft, dass Passwörter, Hashes und Tokens nicht im Debug landen.
 */
class DebugMaskTest extends TestCase
{
    public function testMaskValueShowsOnlyLength(): void
    {
        $this->assertSame('***(32)', \TpLink\Api\Protocol::MaskValue(str_repeat('A', 32)));
    }

    public function testMaskSensitiveJsonString(): void
    {
        $Payload = json_encode(['method' => 'login', 'params' => ['username' => 'user', 'password' => 'secret', 'hashed' => true]]);
        $Masked = \TpLink\Api\Protocol::MaskSensitive($Payload);
        $this->assertStringNotContainsString('secret', $Masked);
        $this->assertStringContainsString('***(6)', $Masked);
        $this->assertStringContainsString('"username":"user"', $Masked);
    }

    public function testMaskSensitiveNestedArray(): void
    {
        $Result = ['error_code' => 0, 'result' => ['token' => 'abcdef', 'stok' => '123', 'digest_passwd' => 'xyz']];
        $Masked = \TpLink\Api\Protocol::MaskSensitive($Result);
        $this->assertSame('***(6)', $Masked['result']['token']);
        $this->assertSame('***(3)', $Masked['result']['stok']);
        $this->assertSame('***(3)', $Masked['result']['digest_passwd']);
        $this->assertSame(0, $Masked['error_code']);
    }

    public function testMaskSensitiveKeepsNonJsonString(): void
    {
        $this->assertSame('no json', \TpLink\Api\Protocol::MaskSensitive('no json'));
    }

    public function testMaskUrl(): void
    {
        $this->assertSame('https://host/stok=***/ds', \TpLink\Api\Protocol::MaskUrl('https://host/stok=AbC123/ds'));
        $this->assertSame('https://host/app?token=***', \TpLink\Api\Protocol::MaskUrl('https://host/app?token=XYZ'));
        $this->assertSame('https://host/app', \TpLink\Api\Protocol::MaskUrl('https://host/app'));
    }
}
