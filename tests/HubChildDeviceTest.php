<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';
include_once __DIR__ . '/stubs/ConstantStubs.php';
require_once __DIR__ . '/../libs/TapoLib.php';

/**
 * Prüft Zuordnung von Modellen/Kategorien und die Variablendefinitionen der Hub-Geräte.
 *
 * Die Beispieldaten (get_device_info) von KE100 und T315 stammen von Geräten des Autors (anonymisiert),
 * die von T100 und T300 sind nachgebildet
 * und nicht an einem Gerät geprüft.
 */
class HubChildDeviceTest extends TestCase
{
    public static function deviceInfoProvider(): array
    {
        return [
            'KE100' => ['trv', [
                'device_id'           => 'X', 'model' => 'KE100', 'category' => 'subg.trv', 'status' => 'online', 'rssi' => -64,
                'at_low_battery'      => false, 'battery_percentage' => 100, 'current_temp' => 20.8, 'target_temp' => 20.0,
                'frost_protection_on' => false, 'child_protection' => false, 'trv_states' => ['heating']
            ]],
            'T315' => ['temp-hmdt-sensor', [
                'device_id'        => 'X', 'model' => 'T315', 'category' => 'subg.trigger.temp-hmdt-sensor', 'status' => 'online',
                'rssi'             => -66, 'at_low_battery' => false, 'battery_percentage' => 100, 'current_temp' => 2.2, 'current_humidity' => 71
            ]],
            'T110' => ['contact-sensor', [
                'device_id'        => 'X', 'model' => 'T110', 'category' => 'subg.trigger.contact-sensor', 'status' => 'online',
                'rssi'             => -35, 'at_low_battery' => false, 'open' => false
            ]],
            'T100' => ['motion-sensor', [
                'device_id'        => 'X', 'model' => 'T100', 'category' => 'subg.trigger.motion-sensor', 'status' => 'online',
                'rssi'             => -73, 'at_low_battery' => false, 'detected' => false
            ]],
            'T300' => ['water-leak-sensor', [
                'device_id'        => 'X', 'model' => 'T300', 'category' => 'subg.trigger.water-leak-sensor', 'status' => 'online',
                'rssi'             => -44, 'at_low_battery' => false, 'battery_percentage' => 100, 'in_alarm' => false,
                'water_leak_status'=> 'water_dry'
            ]],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('deviceInfoProvider')]
    public function testCategoryIdentsExistInDeviceInfo(string $Category, array $Info): void
    {
        $Idents = \TpLink\HubChildDevicesCategory::GetVariableIdentsByCategory($Category);
        $this->assertNotEmpty($Idents, $Category);
        foreach (array_keys($Idents) as $Ident) {
            // battery_percentage fehlt beim T100/T110 (proto-ähnlich: fehlend heißt nicht 0)
            if ($Ident == 'battery_percentage') {
                continue;
            }
            $this->assertArrayHasKey($Ident, $Info, $Category . ': ' . $Ident . ' fehlt in get_device_info');
        }
    }

    public function testNewCategoriesHaveVariables(): void
    {
        $Motion = \TpLink\HubChildDevicesCategory::GetVariableIdentsByCategory('motion-sensor');
        $this->assertArrayHasKey('detected', $Motion);
        $Water = \TpLink\HubChildDevicesCategory::GetVariableIdentsByCategory('water-leak-sensor');
        $this->assertArrayHasKey('water_leak_status', $Water);
        $this->assertArrayHasKey('in_alarm', $Water);
        $this->assertSame([], \TpLink\HubChildDevicesCategory::GetVariableIdentsByCategory('unknown-category'));
    }

    /**
     * Jede Beschriftung der Hub-Geräte muss in der locale.json des Moduls übersetzt sein.
     */
    public function testAllCaptionsAreTranslated(): void
    {
        $Locale = array_merge(
            json_decode(file_get_contents(__DIR__ . '/../libs/locale.json'), true)['translations']['de'],
            json_decode(file_get_contents(__DIR__ . '/../Tapo Hub Device/locale.json'), true)['translations']['de']
        );
        foreach (['trv', 'temp-hmdt-sensor', 'contact-sensor', 'motion-sensor', 'water-leak-sensor'] as $Category) {
            foreach (\TpLink\HubChildDevicesCategory::GetVariableIdentsByCategory($Category) as $Ident => $Params) {
                $this->assertArrayHasKey($Params[\TpLink\IPSVarName], $Locale, $Category . '/' . $Ident . ': Name nicht übersetzt');
                foreach ($Params[\TpLink\IPSVarPresentation]['OPTIONS'] ?? [] as $Option) {
                    $this->assertArrayHasKey($Option['Caption'], $Locale, $Category . '/' . $Ident . ': ' . $Option['Caption'] . ' nicht übersetzt');
                }
            }
        }
    }

    public static function modelProvider(): array
    {
        return [
            ['KE100'], ['T100'], ['T110'], ['T300'], ['T310'], ['T315'], ['S200'], ['S210'], ['S220'],
            ['T100(EU)'], ['T315(US)'], ['S200B'], ['S200D'], ['KE100(UK)']
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modelProvider')]
    public function testHubChildModelsAreMapped(string $Model): void
    {
        $this->assertSame(\TpLink\GUID::HubChild, \TpLink\HubChildDevicesModel::GetGuidByDeviceModel($Model), $Model);
    }

    public static function deviceModelProvider(): array
    {
        return [
            ['P100', \TpLink\GUID::Plug],
            ['P110', \TpLink\GUID::PlugEnergy],
            ['P300(EU)', \TpLink\GUID::PlugsMulti],
            ['S112', \TpLink\GUID::PlugsEnergyMulti],
            ['L530', \TpLink\GUID::BulbColor],
            ['L930', \TpLink\GUID::StripeColor],
            ['H200', \TpLink\GUID::Hub200],
            ['KH100', \TpLink\GUID::Hub100],
            ['XYZ9', ''],
            ['', '']
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('deviceModelProvider')]
    public function testDeviceModelMapping(string $Model, string $Guid): void
    {
        $this->assertSame($Guid, \TpLink\DeviceModel::GetGuidByDeviceModel($Model), $Model);
    }

    public function testUnknownHubChildModel(): void
    {
        $this->assertSame('', \TpLink\HubChildDevicesModel::GetGuidByDeviceModel('X999'));
        $this->assertSame('', \TpLink\HubChildDevicesModel::GetGuidByDeviceModel(''));
    }
}
