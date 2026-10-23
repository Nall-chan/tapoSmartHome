<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/libs/TapoDevice.php';

/**
 * TapoHubIO Klasse für die Kommunikation mit einem Smart Hub.
 * Erweitert \TpLink\Device.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024-2026 Michael Tröger
 * @license       Custom Non-Commercial Share-Alike License (see LICENSE)
 *
 * @version       2.23
 *
 * @property array $SirenTypePresentation
 * @property int $ComponentsTimestamp
 */
class TapoHubIO200 extends \TpLink\Device
{
    /**
     * Mindestabstand in Sekunden, in dem die Fähigkeiten des Hubs (Sirene, LED, SD-Karte) neu gelesen werden.
     * Der Zustand der angelernten Geräte wird bei jeder Abfrage gelesen.
     */
    private const ComponentsInterval = 30;

    /**
     * Create
     *
     * @return void
     */
    public function Create(): void
    {
        //Never delete this line!
        parent::Create();
        $this->ComponentsTimestamp = 0;
    }

    /**
     * ApplyChanges
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        $this->SirenTypePresentation = [];
        $this->ComponentsTimestamp = 0;
        //Never delete this line!
        parent::ApplyChanges();
        if ($this->GetStatus() == IS_ACTIVE) {
            $this->FetchChildDevices();
        }
    }

    /**
     * RequestState
     *
     * @return bool
     */
    public function RequestState(): bool
    {
        if (parent::RequestState()) {
            if ((time() - (int) $this->ComponentsTimestamp) >= self::ComponentsInterval) {
                $this->FetchAppComponents();
                $this->ComponentsTimestamp = time();
            }
            // Eine Anfrage liefert den Zustand aller angelernten Geräte (statt einer Anfrage je Gerät)
            foreach ($this->FetchChildDevices() as $ChildDevice) {
                $this->SendDataToChildren(json_encode(
                    [
                        'DataID'                       => \TpLink\GUID::HubSendToChild,
                        \TpLink\Api\Protocol::Method   => \TpLink\Api\Method::GetDeviceInfo,
                        \TpLink\Property::DeviceId     => $ChildDevice[\TpLink\Api\Result::DeviceID],
                        \TpLink\Api\Result             => $ChildDevice
                    ]
                ));
            }
            return true;
        }
        return false;
    }

    /**
     * ForwardData
     *
     * @param  string $JSONString
     * @return string
     */
    public function ForwardData(string $JSONString): string
    {
        $Data = json_decode($JSONString, true);
        $this->SendDebug('Forward', $Data, 0);
        if ($Data[\TpLink\Api\Protocol::Method] == \TpLink\Api\Method::GetChildDeviceList) {
            return serialize($this->FetchChildDevices());
        }
        $Method = $Data[\TpLink\Api\Protocol::Method];
        $Params = $Data[\TpLink\Api\Protocol::Params];
        $ChildID = $Data[\TpLink\Property::DeviceId];
        if ($ChildID != '') {
            $Params = [
                'childControl'=> [
                    'device_id'   => $ChildID,
                    'request_data'=> [
                        \TpLink\Api\Protocol::Method => $Method,
                        \TpLink\Api\Protocol::Params => $Params
                    ]
                ]
            ];
            $Method = \TpLink\Api\MethodV3::ControlChild;
        }
        $WriteRequest = [
            \TpLink\Api\Protocol::Method=> $Method,
            \TpLink\Api\Protocol::Params=> $Params
        ];

        $Response = $this->SendMultipleRequest([$WriteRequest]);
        if ($Response[$Method] !== null) {
            if ($ChildID != '') {
                $Response = $Response['response_data'];
            }
        }
        return serialize($Response);
    }
    /**
     * ProbeRawRequest
     *
     * Entwicklerfunktion: Sendet eine beliebige Anfrage an den Hub und liefert die rohen Antworten.
     * Nur zum Ermitteln von Protokolldetails, nicht in der Dokumentation.
     *
     * @param  string $Method Name der Methode
     * @param  string $ParamsJson Parameter als JSON, leer für keine
     * @return array
     */
    public function ProbeRawRequest(string $Method, string $ParamsJson = ''): array
    {
        return $this->SendMultipleRequest([
            [
                \TpLink\Api\Protocol::Method => $Method,
                \TpLink\Api\Protocol::Params => ($ParamsJson == '') ? null : json_decode($ParamsJson, true)
            ]
        ]);
    }

    /**
     * ProbeChildRequest
     *
     * Entwicklerfunktion: Sendet eine beliebige Anfrage an ein angelerntes Gerät und liefert die rohe Antwort des Hubs.
     * Nur zum Ermitteln von Protokolldetails, nicht in der Dokumentation.
     *
     * @param  string $DeviceId Geräte ID des Kindes
     * @param  string $Method Name der Methode des Kindes
     * @param  string $ParamsJson Parameter als JSON, leer für keine
     * @return array
     */
    public function ProbeChildRequest(string $DeviceId, string $Method, string $ParamsJson = ''): array
    {
        $Params = [
            'childControl'=> [
                'device_id'   => $DeviceId,
                'request_data'=> [
                    \TpLink\Api\Protocol::Method => $Method,
                    \TpLink\Api\Protocol::Params => ($ParamsJson == '') ? null : json_decode($ParamsJson, true)
                ]
            ]
        ];
        return $this->SendMultipleRequest([
            [
                \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::ControlChild,
                \TpLink\Api\Protocol::Params => $Params
            ]
        ]);
    }

    /**
     * GetTriggerLogs
     *
     * Liest das Ereignisprotokoll (Kontakt geöffnet/geschlossen, Bewegung, Tastendruck, ...) eines am Hub angelernten Gerätes.
     *
     * @param  string $DeviceId Geräte ID des Kindes (Spalte `Geräte ID` im Hub Configurator)
     * @param  int $StartId ID des Eintrags, ab dem rückwärts gelesen wird, `0` liefert die neuesten Einträge
     * @param  int $PageSize Maximale Anzahl der Einträge
     * @return false|array false bei Fehler, sonst das Ergebnis des Hubs (`start_id`, `sum`, `logs`, neueste Einträge zuerst)
     */
    public function GetTriggerLogs(string $DeviceId, int $StartId = 0, int $PageSize = 10): false|array
    {
        if (($DeviceId == '') || ($PageSize < 1)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }
        $Params = [
            'childControl'=> [
                'device_id'   => $DeviceId,
                'request_data'=> [
                    \TpLink\Api\Protocol::Method => 'get_trigger_logs',
                    \TpLink\Api\Protocol::Params => ['page_size' => $PageSize, 'start_id' => $StartId]
                ]
            ]
        ];
        $Response = $this->SendMultipleRequest([
            [
                \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::ControlChild,
                \TpLink\Api\Protocol::Params => $Params
            ]
        ]);
        $this->SendDebug('TriggerLogs', $Response, 0);
        $ResponseData = $Response[\TpLink\Api\MethodV3::ControlChild]['response_data'] ?? null;
        if (!is_array($ResponseData)) {
            return false;
        }
        if (($ResponseData[\TpLink\Api\ErrorCode] ?? -1) != \TpLink\Api\ErrorCodes::Success) {
            trigger_error(($ResponseData[\TpLink\Api\ErrorCode] ?? '') . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText((int) ($ResponseData[\TpLink\Api\ErrorCode] ?? 0))), E_USER_NOTICE);
            return false;
        }
        return $ResponseData[\TpLink\Api\Result];
    }

    /**
     * StartSiren
     *
     * Startet die Sirene des Hubs mit den übergebenen Werten und stellt danach die vorher gelesene Konfiguration wieder her.
     *
     * @param  int $Duration Dauer in Sekunden (1 bis 300)
     * @param  int $Volume Lautstärke (1 bis 10)
     * @param  string $SirenType Name des Klingeltons, z. B. 'Alarm 5' oder 'Doorbell Ring 1'
     * @return bool true wenn die Sirene gestartet und die Konfiguration wiederhergestellt wurde
     */
    public function StartSiren(int $Duration, int $Volume, string $SirenType): bool
    {
        $Response = $this->SendMultipleRequest(
            [
                [
                    \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetSirenConfig,
                    \TpLink\Api\Protocol::Params=> [
                        'siren'=> new stdClass()
                    ]
                ],
                [
                    \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetSirenConfig,
                    \TpLink\Api\Protocol::Params=> [
                        'siren'=> [
                            'duration'  => $Duration,
                            'volume'    => (string) $Volume,
                            'siren_type'=> $SirenType
                        ]
                    ]
                ],
                [
                    \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetSirenStatus,
                    \TpLink\Api\Protocol::Params=> [
                        'siren'=> [
                            'status'=> 'on'
                        ]
                    ]
                ]
            ]
        );
        $this->SendDebug('Write Response', $Response, 0);
        if (isset($Response[\TpLink\Api\MethodV3::GetSirenConfig])) {
            $Response2 = $this->SendMultipleRequest(
                [
                    [
                        \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetSirenConfig,
                        \TpLink\Api\Protocol::Params=> [
                            'siren'=> [
                                'duration'  => intval($Response[\TpLink\Api\MethodV3::GetSirenConfig]['duration']),
                                'volume'    => $Response[\TpLink\Api\MethodV3::GetSirenConfig]['volume'],
                                'siren_type'=> $Response[\TpLink\Api\MethodV3::GetSirenConfig]['siren_type']
                            ]
                        ]
                    ]
                ]
            );
            // Erfolg: Sirene wurde gestartet (Antwort der ersten Anfrage) und die ursprüngliche Konfiguration wiederhergestellt (zweite Anfrage)
            return isset($Response[\TpLink\Api\MethodV3::SetSirenStatus]) && isset($Response2[\TpLink\Api\MethodV3::SetSirenConfig]);
        }
        return false;
    }
    /**
     * processSpecialReadResponse
     *
     * @param  string $Method
     * @param  array $Response
     * @return array
     */
    protected function processSpecialReadResponse(string $Method, array $Response): ?array
    {
        $Variables = [];
        switch ($Method) {
            case \TpLink\Api\MethodV3::GetSirenTypeList:
                foreach ($Response['siren_type_list'] as $Type) {
                    $SirenTypes[] =
                        [
                            'Value'              => $Type,
                            'Caption'            => $Type,
                            'IconActive'         => false,
                            'IconValue'          => '',
                            'Color'              => -1
                        ];
                }
                $this->SirenTypePresentation = $SirenTypes;
                break;
            case \TpLink\Api\MethodV3::GetSirenConfig:
                $Variables = \TpLink\Components\Siren::$Variables;
                unset($Variables['Siren__siren__status']);
                $Variables['Siren__siren__volume']['Value'] = intval($Response['volume']);
                $Variables['Siren__siren__duration']['Value'] = intval($Response['duration']);
                $Variables['Siren__siren__siren_type'][\TpLink\IPSVarPresentation]['OPTIONS'] = $this->SirenTypePresentation;
                $Variables['Siren__siren__siren_type']['Value'] = $Response['siren_type'];
                break;
            case \TpLink\Api\MethodV3::GetSirenStatus:
                $Variables['Siren__siren__status'] = \TpLink\Components\Siren::$Variables['Siren__siren__status'];
                $Variables['Siren__siren__status']['Value'] = $Response['status'] == 'on';
                break;
            default:
                return null;
        }
        return $Variables;
    }

    /**
     * processSpecialWritePayload
     *
     * @param  string $Class
     * @param  string $Ident
     * @param  mixed $Value
     * @return bool
     */
    protected function processSpecialWritePayload(string $Class, string $Ident, mixed $Value): bool
    {
        $this->SendDebug('Write Payload', ['Class' => $Class, 'Ident' => $Ident, 'Value' => $Value], 0);
        switch ($Ident) {
            case 'Siren__siren__status':
                $Method = \TpLink\Api\MethodV3::SetSirenStatus;
                $WriteRequest = [
                    \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetSirenStatus,
                    \TpLink\Api\Protocol::Params=> [
                        'siren'=> [
                            'status'=> $Value ? 'on' : 'off'
                        ]
                    ]
                ];
                break;
            case 'Siren__siren__duration':
                $Method = \TpLink\Api\MethodV3::SetSirenConfig;
                $WriteRequest = [
                    \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetSirenConfig,
                    \TpLink\Api\Protocol::Params=> [
                        'siren'=> [
                            'duration'=> intval($Value)
                        ]
                    ]
                ];
                break;
            default:
                return false;
        }
        $Response = $this->SendMultipleRequest([$WriteRequest]);
        if (isset($Response[$Method])) {
            $this->SendDebug('Write Response', $Response, 0);
            $this->SetValue($Ident, $Value);
        }
        return true;
    }

    /**
     * FetchChildDevices
     *
     * @return void
     */
    private function FetchChildDevices(): array
    {
        $ChildIDs = [];
        $ChildData = [];
        $StartIndex = 0;
        do {
            $Response = $this->SendMultipleRequest([
                [
                    \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::GetChildDeviceList,
                    \TpLink\Api\Protocol::Params => [
                        'childControl'=> [
                            'start_index' => $StartIndex
                        ]
                    ]
                ]
            ]);
            $Result = $Response[\TpLink\Api\MethodV3::GetChildDeviceList] ?? null;
            if (!is_array($Result) || !isset($Result[\TpLink\Api\Result::ChildList]) || !count($Result[\TpLink\Api\Result::ChildList])) {
                break;
            }
            foreach ($Result[\TpLink\Api\Result::ChildList] as $ChildDevice) {
                $ChildData[] = $ChildDevice;
                $ChildIDs[] = $ChildDevice[\TpLink\Api\Result::DeviceID];
            }
            $StartIndex = count($ChildData);
        } while ($StartIndex < (int) ($Result['sum'] ?? 0));
        $this->ChildIDs = $ChildIDs;
        $this->SendDebug('Childs', $ChildIDs, 0);
        return $ChildData;
    }
}
