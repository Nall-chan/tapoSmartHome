<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';
include_once __DIR__ . '/stubs/ConstantStubs.php';
require_once __DIR__ . '/../libs/TapoLib.php';

/**
 * Prüft die Ermittlung der Broadcast-Adressen für die Suche im Netzwerk.
 */
class NetworkTest extends TestCase
{
    public function testBroadcastForClassCNetwork(): void
    {
        $Targets = \TpLink\Network::GetBroadcastTargets(['eth0' => self::Interface('192.168.201.10', '255.255.255.0')]);
        $this->assertContains(['Source' => '192.168.201.10', 'Target' => '192.168.201.255'], $Targets);
    }

    public function testBroadcastForOtherPrefixLengths(): void
    {
        $Targets = \TpLink\Network::GetBroadcastTargets([
            'a' => self::Interface('172.27.224.1', '255.255.240.0'),
            'b' => self::Interface('10.1.2.3', '255.0.0.0'),
            'c' => self::Interface('192.168.10.130', '255.255.255.128')
        ]);
        $this->assertContains(['Source' => '172.27.224.1', 'Target' => '172.27.239.255'], $Targets);
        $this->assertContains(['Source' => '10.1.2.3', 'Target' => '10.255.255.255'], $Targets);
        $this->assertContains(['Source' => '192.168.10.130', 'Target' => '192.168.10.255'], $Targets);
    }

    public function testLoopbackLinkLocalAndHostRoutesAreSkipped(): void
    {
        $Targets = \TpLink\Network::GetBroadcastTargets([
            'lo'    => self::Interface('127.0.0.1', '255.0.0.0'),
            'apipa' => self::Interface('169.254.63.234', '255.255.0.0'),
            'ppp'   => self::Interface('10.8.0.2', '255.255.255.255')
        ]);
        $this->assertSame([['Source' => '0.0.0.0', 'Target' => '255.255.255.255']], $Targets);
    }

    public function testIPv6AndInvalidEntriesAreSkipped(): void
    {
        $Targets = \TpLink\Network::GetBroadcastTargets([
            'v6'   => ['unicast' => [['family' => 23, 'address' => 'fe80::1', 'netmask' => 'ffff:ffff:ffff:ffff::']]],
            'bad'  => ['unicast' => [['address' => 'x', 'netmask' => 'y']]],
            'none' => []
        ]);
        $this->assertSame([['Source' => '0.0.0.0', 'Target' => '255.255.255.255']], $Targets);
    }

    public function testFallbackIsAlwaysLastAndAddressesAreUnique(): void
    {
        $Targets = \TpLink\Network::GetBroadcastTargets([
            'a' => self::Interface('192.168.1.5', '255.255.255.0'),
            'b' => self::Interface('192.168.1.5', '255.255.255.0')
        ]);
        $this->assertCount(2, $Targets);
        $this->assertSame(['Source' => '0.0.0.0', 'Target' => '255.255.255.255'], end($Targets));
    }

    public function testEmptyInterfaceListKeepsFallback(): void
    {
        $this->assertSame([['Source' => '0.0.0.0', 'Target' => '255.255.255.255']], \TpLink\Network::GetBroadcastTargets([]));
    }
    private static function Interface(string $Address, string $Netmask): array
    {
        return ['unicast' => [['flags' => 0, 'family' => 2, 'address' => $Address, 'netmask' => $Netmask]]];
    }
}
