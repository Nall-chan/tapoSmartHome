<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/libs/TapoDevice.php';

/**
 * TapoCamera Klasse für die Kommunikation mit einer Camera.
 * Erweitert \TpLink\Device.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.70
 *
 * @property array $MsgAlarmType
 * @property array $MsgPushType
 */
class TapoCamera extends \TpLink\Device
{
    /**
     * Create
     *
     * @return void
     */
    public function Create(): void
    {
        $this->RegisterPropertyString(\TpLink\Property::UsernameCamera, '');
        $this->RegisterPropertyString(\TpLink\Property::PasswordCamera, '');
        $this->RegisterPropertyString(\TpLink\Property::Stream, 'stream1');
        $this->MsgAlarmType = [];
        $this->MsgPushType = [];
        //Never delete this line!
        parent::Create();
    }
    /**
     * ApplyChanges
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        $this->MsgAlarmType = [];
        $this->MsgPushType = [];
        //Never delete this line!
        parent::ApplyChanges();
        $Username = $this->ReadPropertyString(\TpLink\Property::UsernameCamera);
        $Password = $this->ReadPropertyString(\TpLink\Property::PasswordCamera);
        $Host = $this->ReadPropertyString(\TpLink\Property::Host);
        $Stream = $this->ReadPropertyString(\TpLink\Property::Stream);
        $StreamURL = 'rtsp://' . urlencode($Username) . ':' . urlencode($Password) . '@' . $Host . ':554/' . $Stream;
        $this->SendDebug('MediaURL', $StreamURL, 0);
        if ($StreamURL) {
            $this->SetMedia($StreamURL);
        }
    }

    public function RequestState(): bool
    {
        if (parent::GetDeviceInfo()) {
            $this->FetchAppComponents();
            return true;
        }
        return false;
    }
    /**
     * GetConfigurationForm
     *
     * @return string
     */
    public function GetConfigurationForm(): string
    {
        $Form = json_decode(parent::GetConfigurationForm(), true);
        $AddElements = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        $Form['elements'] = array_merge(
            $Form['elements'],
            $AddElements['elements']
        );
        $Form['translations']['de'] = array_merge($AddElements['translations']['de'], $Form['translations']['de']);
        return json_encode($Form);
    }

    protected function processSpecialReadResponse(string $Method, array $Response): ?array
    {
        $Variables = [];
        switch ($Method) {
            case \TpLink\Api\MethodV3::GetPresetConfig:
                if (!isset($Response['preset']['preset'])) {
                    return null;
                }
                $Response = $Response['preset']['preset'];
                $Presets = [];
                foreach ($Response['id'] as $Index => $Id) {
                    $Presets[] =
                        [
                            'Value'              => (int) $Id,
                            'Caption'            => $Response['name'][$Index],
                            'IconActive'         => false,
                            'IconValue'          => '',
                            'Color'              => -1
                        ];
                }
                if (count($Presets) == 0) {
                    return [];
                }
                $Variables = [
                    'Ptz__preset__goto_preset__id' => [
                        \TpLink\IPSVarName        => 'PTZ Presets',
                        \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                        \TpLink\IPSVarPresentation=> [
                            \TpLink\PRESENTATION => VARIABLE_PRESENTATION_ENUMERATION,
                            'OPTIONS'            => $Presets
                        ],
                        \TpLink\HasAction      => true
                    ]
                ];
                break;
            case \TpLink\Api\MethodV3::GetAlertEventType:
                if (!isset($Response['msg_alarm']['msg_alarm_type'])) {
                    return null;
                }
                $Response = $Response['msg_alarm']['msg_alarm_type'];
                $MsgAlarmType = [];
                foreach ($Response as $Index => $AlarmType) {
                    $MsgAlarmType[$Index] = [
                        'name'   => $AlarmType['name'],
                        'enabled'=> $AlarmType['enabled']
                    ];
                    $Variables['Alert__msg_alarm__msg_alarm_type__' . $Index . '__enabled'] =
                    [
                        \TpLink\IPSVarName        => 'Alert ' . ucwords(str_replace('_', ' ', $AlarmType['name'])) . ' enabled',
                        \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                        \TpLink\IPSVarPresentation=> [
                            \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                        ],
                        \TpLink\HasAction      => true,
                        'Value'                => $AlarmType['enabled'] == 'on'
                    ];
                }
                $this->MsgAlarmType = $MsgAlarmType;
                break;
            case \TpLink\Api\MethodV3::GetMsgPushEventList:
                if (!isset($Response['msg_push']['msg_push_event'])) {
                    return null;
                }
                $Response = $Response['msg_push']['msg_push_event'];
                $MsgPushType = [];
                foreach ($Response as $Index => $PushType) {
                    $MsgPushType[$Index] = [
                        'name'   => $PushType['name'],
                        'enabled'=> $PushType['enabled']
                    ];
                    $Variables['MsgPush__msg_push__msg_push_event__' . $Index . '__enabled'] =
                    [
                        \TpLink\IPSVarName        => 'Push Notification ' . ucwords(str_replace('_', ' ', $PushType['name'])) . ' enabled',
                        \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                        \TpLink\IPSVarPresentation=> [
                            \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                        ],
                        \TpLink\HasAction      => true,
                        'Value'                => $PushType['enabled'] == 'on'
                    ];
                }
                $this->MsgPushType = $MsgPushType;
                break;
            default:
                return null;
        }
        return $Variables;
    }

    protected function processSpecialWritePayload(string $Class, string $Ident, mixed $Value): bool
    {
        $this->SendDebug('Write Payload', ['Class' => $Class, 'Ident' => $Ident, 'Value' => $Value], 0);
        preg_match('/__(\d+)__/', $Ident, $Matches);
        if (!isset($Matches[1])) {
            return false;
        }
        $Index = (int) $Matches[1];
        switch ($Class) {
            case 'Alert':

                $MsgAlarmType = $this->MsgAlarmType;
                if (!isset($MsgAlarmType[$Index])) {
                    return false;
                }
                $MsgAlarmType[$Index]['enabled'] = $Value ? 'on' : 'off';
                $this->MsgAlarmType = $MsgAlarmType;
                $Method = \TpLink\Api\MethodV3::SetAlertEventType;
                $WriteRequest = [
                    \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetAlertEventType,
                    \TpLink\Api\Protocol::Params=> [
                        'msg_alarm'=> [
                            'msg_alarm_type'=> $MsgAlarmType
                        ]
                    ]
                ];
                break;
            case 'MsgPush':
                $MsgPushType = $this->MsgPushType;
                if (!isset($MsgPushType[$Index])) {
                    return false;
                }
                $MsgPushType[$Index]['enabled'] = $Value ? 'on' : 'off';
                $this->MsgPushType = $MsgPushType;
                $Method = \TpLink\Api\MethodV3::SetAlertEventType;
                $WriteRequest = [
                    \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetMsgPushEventList,
                    \TpLink\Api\Protocol::Params=> [
                        'msg_push'=> [
                            'msg_push_event'=> $MsgPushType
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
    protected function SetMedia(string $StreamURL): void
    {
        IPS_SetMediaFile($this->GetMediaId(), $StreamURL, false);
    }
    protected function GetMediaId(): int
    {
        $MediaId = $this->FindIDForIdent('STREAM');
        if (!$MediaId) {
            $MediaId = IPS_CreateMedia(MEDIATYPE_STREAM);
            IPS_SetParent($MediaId, $this->InstanceID);
            IPS_SetName($MediaId, $this->Translate('Stream'));
            IPS_SetIdent($MediaId, 'STREAM');
        }
        return $MediaId;
    }

}
