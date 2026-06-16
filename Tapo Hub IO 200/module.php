<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/libs/TapoDevice.php';

/**
 * TapoHubIO Klasse für die Kommunikation mit einem Smart Hub.
 * Erweitert \TpLink\Device.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.70
 *
 * @property array $SirenTypePresentation
 */
class TapoHubIO200 extends \TpLink\Device
{
    /**
     * ApplyChanges
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        $this->SirenTypePresentation = [];
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
            $this->FetchAppComponents();
            foreach ($this->ChildIDs as $ChildID) {
                $Params = [
                    'childControl' => [
                        'device_id'   => $ChildID,
                        'request_data'=> [
                            \TpLink\Api\Protocol::Method => \TpLink\Api\Method::GetDeviceInfo,
                            \TpLink\Api\Protocol::Params => null
                        ]
                    ]
                ];
                $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\MethodV3::ControlChild, '', $Params);
                $Response = $this->SendMultipleRequest([$Request]);
                if (!isset($Response[\TpLink\Api\MethodV3::ControlChild]['response_data'])) {
                    continue;
                }
                $Response = $Response[\TpLink\Api\MethodV3::ControlChild]['response_data'];
                $this->SendDebug('Response', $Response, 0);
                if ($Response[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($Response[\TpLink\Api\ErrorCode] . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($Response[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
                    restore_error_handler();
                    continue;
                }
                $this->SendDataToChildren(json_encode(
                    [
                        'DataID'                       => \TpLink\GUID::HubSendToChild,
                        \TpLink\Api\Protocol::Method   => \TpLink\Api\Method::GetDeviceInfo,
                        \TpLink\Property::DeviceId     => $ChildID,
                        \TpLink\Api\Result             => $Response[\TpLink\Api\Result]
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
            return true;
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
        $Response = $this->SendMultipleRequest([
            [
                \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::GetChildDeviceList,
                \TpLink\Api\Protocol::Params => [
                    'childControl'=> [
                        'start_index' => 0
                    ]
                ]
            ]/*,
            [
                \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::GetChildDeviceComponentList,
                \TpLink\Api\Protocol::Params => [
                    'childControl'=> [
                        'start_index' => 0
                    ]
                ]
            ]*/
        ]);
        $ChildIDs = [];
        $ChildData = [];
        if (isset($Response[\TpLink\Api\MethodV3::GetChildDeviceList][\TpLink\Api\Result::ChildList])) {
            $ChildData = $Response[\TpLink\Api\MethodV3::GetChildDeviceList][\TpLink\Api\Result::ChildList];
            foreach ($ChildData as $ChildDevice) {
                $ChildIDs[] = $ChildDevice[\TpLink\Api\Result::DeviceID];
            }
        }
        $this->ChildIDs = $ChildIDs;
        $this->SendDebug('Childs', $ChildIDs, 0);
        return $ChildData;
    }
}
