<?php

declare(strict_types=1);
eval('declare(strict_types=1);namespace TapoDiscovery {?>' . file_get_contents(dirname(__DIR__) . '/libs/helper/DebugHelper.php') . '}');
require_once dirname(__DIR__) . '/libs/TapoCrypt.php';
require_once dirname(__DIR__) . '/libs/TapoLib.php';

/**
 * TapoDiscovery
 * TapoDiscovery Klasse für das auffinden von Geräten im Netzwerk.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024-2026 Michael Tröger
 * @license       Custom Non-Commercial Share-Alike License (see LICENSE)
 *
 * @version       2.23
 * @method bool SendDebug(string $Message, mixed $Data, int $Format)
 *
 */
class TapoDiscovery extends IPSModuleStrict
{
    use \TapoDiscovery\DebugHelper;

    public const DISCOVERY_TIMEOUT = 4;

    /**
     * Create
     *
     * @return void
     */
    public function Create(): void
    {
        //Never delete this line!
        parent::Create();
        $this->RegisterAttributeString(\TpLink\Attribute::Username, '');
        $this->RegisterAttributeString(\TpLink\Attribute::Password, '');
    }

    /**
     * RequestAction
     *
     * @param  string $Ident
     * @param  mixed $Value
     * @return void
     */
    public function RequestAction(string $Ident, mixed $Value): void
    {
        if ($Ident == 'Save') {
            $Data = explode(':', $Value);
            $this->WriteAttributeString(\TpLink\Attribute::Username, urldecode($Data[0]));
            $this->WriteAttributeString(\TpLink\Attribute::Password, urldecode($Data[1]));
            $this->ReloadForm();
            return;
        }
    }

    /**
     * GetConfigurationForm
     *
     * @return string
     */
    public function GetConfigurationForm(): string
    {
        $Form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        if ($this->GetStatus() == IS_CREATING) {
            return json_encode($Form);
        }

        if (IPS_GetOption('NATSupport') && strpos(IPS_GetKernelPlatform(), 'Docker')) {
            // not supported. Docker cannot forward Broadcast :(
            $Form['actions'][3]['visible'] = true;
            $this->SendDebug('FORM', json_encode(self::MaskFormPassword($Form)), 0);
            $this->SendDebug('FORM', json_last_error_msg(), 0);
            return json_encode($Form);
        }
        $Form['actions'][0]['items'][0]['items'][0]['value'] = $this->ReadAttributeString(\TpLink\Attribute::Username);
        $Form['actions'][0]['items'][0]['items'][1]['value'] = $this->ReadAttributeString(\TpLink\Attribute::Password);
        $Values = $this->GetDevices();
        $Form['actions'][1]['values'] = $Values;
        $Unsupported = [];
        foreach ($Values as $Value) {
            if (isset($Value['encryptType'])) {
                $Unsupported[$Value['encryptType']] = true;
            }
        }
        if (count($Unsupported) > 0) {
            $Form['actions'][2]['caption'] = sprintf($this->Translate('Devices with the unsupported encryption scheme %s were found. They cannot be created.'), implode(', ', array_keys($Unsupported)));
            $Form['actions'][2]['visible'] = true;
        }
        $this->SendDebug('FORM', json_encode(self::MaskFormPassword($Form)), 0);
        $this->SendDebug('FORM', json_last_error_msg(), 0);

        return json_encode($Form);
    }

    /**
     * GetDevices
     *
     * @return array
     */
    private function GetDevices(): array
    {
        $Devices = $this->Discover();
        $this->SendDebug('NetworkDevices', $Devices, 0);
        $IPSDevices = $this->GetIPSInstances();
        $this->SendDebug('IPS Devices', $IPSDevices, 0);
        $Values = [];
        foreach ($Devices as $Device) {
            $Guid = \TpLink\DeviceModel::GetGuidByDeviceModel($Device[\TpLink\Api\Result::DeviceModel]);
            if ($Guid == '') {
                $Guid = \TpLink\DeviceType::GetGuidByDeviceType($Device[\TpLink\Api\Result::DeviceType]);
                if ($Guid == '') {
                    continue;
                }
            }
            $InstanceID = array_search(strtoupper($Device[\TpLink\Api\Result::Mac]), $IPSDevices);
            if ($InstanceID) {
                $Open = false;
                if (\TpLink\GUID::GuidIsHub($Guid)) {
                    $ConnectionID = IPS_GetInstance($InstanceID)['ConnectionID'];
                    if (IPS_InstanceExists($ConnectionID)) {
                        $Open = IPS_GetProperty($ConnectionID, \TpLink\Property::Open);
                    }
                } else {
                    $Open = IPS_GetProperty($InstanceID, \TpLink\Property::Open);
                }
                unset($IPSDevices[$InstanceID]);
            } else {
                $Open = true;
            }
            $EncryptType = $Device[\TpLink\Api\Result::MGT][\TpLink\Api\Result::EncryptType] ?? 'AES';
            if (!in_array(strtoupper((string) $EncryptType), ['AES', 'KLAP'])) {
                // z.B. TPAP (neuere Firmware): wird nicht unterstützt, Instanz kann nicht angelegt werden
                $this->SendDebug('Unsupported encryption', $Device[\TpLink\Api\Result::Ip] . ': ' . $EncryptType, 0);
                $Values[] = [
                    'host'               => $Device[\TpLink\Api\Result::Ip],
                    'mac'                => strtoupper($Device[\TpLink\Api\Result::Mac]),
                    'name'               => ($InstanceID ? IPS_GetName($InstanceID) : $Device[\TpLink\Api\Result::DeviceType]),
                    'device_type'        => $Device[\TpLink\Api\Result::DeviceType],
                    'device_model'       => $Device[\TpLink\Api\Result::DeviceModel],
                    'instanceID'         => ($InstanceID ? $InstanceID : 0),
                    'encryptType'        => strtoupper((string) $EncryptType),
                    'rowColor'           => '#FFC0C0'
                ];
                continue;
            }
            $Protocol = \TpLink\Crypt\HTTP;
            if (isset($Device[\TpLink\Api\Result::MGT][\TpLink\Api\Result::Protocol])) {
                $Protocol = $Device[\TpLink\Api\Result::MGT][\TpLink\Api\Result::Protocol] ? \TpLink\Crypt\HTTPS : \TpLink\Crypt\HTTP;
            }
            $Create = [
                'moduleID'                => $Guid,
                'configuration'           => [
                    \TpLink\Property::Open              => $Open,
                    \TpLink\Property::Host              => $Device[\TpLink\Api\Result::Ip],
                    \TpLink\Property::Mac               => $Device[\TpLink\Api\Result::Mac],
                    \TpLink\Property::Username          => $this->ReadAttributeString(\TpLink\Attribute::Username),
                    \TpLink\Property::Password          => $this->ReadAttributeString(\TpLink\Attribute::Password),
                    \TpLink\Property::EncryptType       => strtoupper((string) $EncryptType),
                    \TpLink\Property::Protocol          => $Protocol
                ]
            ];
            if (\TpLink\GUID::GuidIsHub($Guid)) {
                $Create = [[
                    'moduleID'                => \TpLink\GUID::HubConfigurator,
                    'configuration'           => new stdClass()
                ], $Create];
            }
            $Values[] = [
                'host'               => $Device[\TpLink\Api\Result::Ip],
                'mac'                => strtoupper($Device[\TpLink\Api\Result::Mac]),
                'name'               => ($InstanceID ? IPS_GetName($InstanceID) : $Device[\TpLink\Api\Result::DeviceType]),
                'device_type'        => $Device[\TpLink\Api\Result::DeviceType],
                'device_model'       => $Device[\TpLink\Api\Result::DeviceModel],
                'instanceID'         => ($InstanceID ? $InstanceID : 0),
                'create'             => $Create
            ];
        }
        foreach ($IPSDevices as $InstanceID => $Mac) {
            $GUID = IPS_GetInstance($InstanceID)['ModuleInfo']['ModuleID'];
            $Host = '';
            if ($GUID == \TpLink\GUID::HubConfigurator) {
                $ConnectionID = IPS_GetInstance($InstanceID)['ConnectionID'];
                if (IPS_InstanceExists($ConnectionID)) {
                    $Host = IPS_GetProperty($ConnectionID, \TpLink\Property::Host);
                }
            } else {
                $Host = IPS_GetProperty($InstanceID, \TpLink\Property::Host);
            }

            $Values[] = [
                'host'               => $Host,
                'mac'                => $Mac,
                'name'               => IPS_GetName($InstanceID),
                'device_type'        => '',
                'device_model'       => '',
                'instanceID'         => $InstanceID
            ];
        }
        return $Values;
    }

    /**
     * MaskFormPassword
     *
     * Ersetzt Passwörter im Formular (Eingabefeld und Konfiguration der anzulegenden Instanzen) für die Debug-Ausgabe.
     *
     * @param  array $Data
     * @return array
     */
    private static function MaskFormPassword(array $Data): array
    {
        foreach ($Data as $Key => $Value) {
            if (is_array($Value)) {
                $Data[$Key] = self::MaskFormPassword($Value);
            } elseif ($Key === \TpLink\Property::Password && is_string($Value)) {
                $Data[$Key] = \TpLink\Api\Protocol::MaskValue($Value);
            }
        }
        if (($Data['name'] ?? '') === \TpLink\Property::Password && is_string($Data['value'] ?? null)) {
            $Data['value'] = \TpLink\Api\Protocol::MaskValue($Data['value']);
        }
        return $Data;
    }

    /**
     * GetBroadcastTargets
     *
     * @return array Liste von ['Source' => lokale IP, 'Target' => Broadcast-Adresse], siehe \TpLink\Network
     */
    private function GetBroadcastTargets(): array
    {
        $Interfaces = function_exists('net_get_interfaces') ? @net_get_interfaces() : [];
        return \TpLink\Network::GetBroadcastTargets(is_array($Interfaces) ? $Interfaces : []);
    }

    /**
     * Discover
     *
     * Sendet die Suchanfrage über alle IPv4-Netzwerkschnittstellen und sammelt die Antworten.
     *
     * @param  bool $Retry Bei keiner Antwort einmal wiederholen
     * @return array Antworten der Geräte, Schlüssel ist die IP-Adresse
     */
    private function Discover(bool $Retry = true): array
    {
        $Key = (new \phpseclib\Crypt\RSA())->createKey(1024);
        $JsonPayload = \TpLink\Api\Protocol::BuildDiscoveryRequest($Key['publickey']);
        $Part1 = "\x02\x00\x00\x01" . pack('n', strlen($JsonPayload)) . "\x11\x00" . "\x01\x02\x03\x04";
        $Payload = $Part1 . hash('crc32b', $Part1 . "\x5A\x6B\x7C\x8D" . $JsonPayload, true) . $JsonPayload;
        $DevicesData = [];
        $Sockets = [];
        foreach ($this->GetBroadcastTargets() as $BroadcastTarget) {
            $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($socket === false) {
                $this->SendDebug('Error', 'on create broadcast Socket', 0);
                continue;
            }
            socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
            socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, 1);
            if (!@socket_bind($socket, $BroadcastTarget['Source'], 0)) {
                $this->SendDebug('Error', 'on bind ' . $BroadcastTarget['Source'], 0);
                @socket_close($socket);
                continue;
            }
            $this->SendDebug('Search', $BroadcastTarget['Source'] . ' -> ' . $BroadcastTarget['Target'], 0);
            if (@socket_sendto($socket, $Payload, strlen($Payload), 0, $BroadcastTarget['Target'], 20002) === false) {
                $this->SendDebug('Error', 'on send discovery message via ' . $BroadcastTarget['Source'], 0);
                @socket_close($socket);
                continue;
            }
            $Sockets[] = $socket;
        }
        $discoveryTimeout = time() + self::DISCOVERY_TIMEOUT;
        while (count($Sockets) && (time() < $discoveryTimeout)) {
            $Read = $Sockets;
            $Write = null;
            $Except = null;
            if (@socket_select($Read, $Write, $Except, 0, 500000) < 1) {
                continue;
            }
            foreach ($Read as $socket) {
                $response = '';
                $IPAddress = '';
                $Port = 0;
                if (!@socket_recvfrom($socket, $response, 2048, 0, $IPAddress, $Port)) {
                    continue;
                }
                $Data = substr($response, 16);
                $this->SendDebug('Receive (' . $IPAddress . ')', $Data, 0);
                $JsonReceive = json_decode($Data, true);
                if (!$JsonReceive) {
                    continue;
                }
                if ($JsonReceive[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                    continue;
                }
                $DevicesData[$IPAddress] = $JsonReceive[\TpLink\Api\Result];
            }
        }
        foreach ($Sockets as $socket) {
            socket_close($socket);
        }
        if ((count($DevicesData) == 0) && $Retry) {
            usleep(2000000);
            $DevicesData = $this->Discover(false);
        }
        return $DevicesData;
    }

    /**
     * GetIPSInstances
     *
     * @return array
     */
    private function GetIPSInstances(): array
    {
        $Devices = [];
        foreach (\TpLink\Guid::$GUIDs as $GUID) {
            $InstanceIDList = IPS_GetInstanceListByModuleID($GUID);
            foreach ($InstanceIDList as $InstanceID) {
                if ($GUID == \TpLink\GUID::HubConfigurator) {
                    $Devices[$InstanceID] = '';
                    $ConnectionID = IPS_GetInstance($InstanceID)['ConnectionID'];
                    if (IPS_InstanceExists($ConnectionID)) {
                        $Devices[$InstanceID] = strtoupper(IPS_GetProperty($ConnectionID, \TpLink\Property::Mac));
                    }
                } else {
                    $Devices[$InstanceID] = strtoupper(IPS_GetProperty($InstanceID, \TpLink\Property::Mac));
                }
            }
        }
        return $Devices;
    }
}
