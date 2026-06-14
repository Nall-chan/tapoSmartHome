<?php

declare(strict_types=1);

/**
 * TapoDevice
 * Enthält die Basisklasse für Netzwerk-Geräte.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.70
 */

namespace {
    eval('declare(strict_types=1);namespace Tapo {?>' . file_get_contents(__DIR__ . '/helper/BufferHelper.php') . '}');
    eval('declare(strict_types=1);namespace Tapo {?>' . file_get_contents(__DIR__ . '/helper/DebugHelper.php') . '}');
    eval('declare(strict_types=1);namespace Tapo {?>' . file_get_contents(__DIR__ . '/helper/SemaphoreHelper.php') . '}');
    eval('declare(strict_types=1);namespace Tapo {?>' . file_get_contents(__DIR__ . '/helper/VariableHelper.php') . '}');
    eval('declare(strict_types=1);namespace Tapo {?>' . file_get_contents(__DIR__ . '/helper/VariableProfileHelper.php') . '}');
    eval('declare(strict_types=1);namespace Tapo {?>' . file_get_contents(__DIR__ . '/helper/AttributeArrayHelper.php') . '}');
    require_once 'TapoCrypt.php';
    require_once 'TapoLib.php';
    require_once 'VariableIdent.php';
}

namespace TpLink
{
    /**
     * Device Basisklasse für Netzwerk-Geräte.
     * Erweitert IPSModule.
     *
     * @author        Michael Tröger <micha@nall-chan.net>
     * @copyright     2024 Michael Tröger
     * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
     *
     * @version       1.70
     *
     * @property string $terminalUUID
     * @property string $cookie
     * @property string $TpLinkCipherIV
     * @property string $TpLinkCipherKey
     * @property string $KlapLocalSeed
     * @property string $KlapRemoteSeed
     * @property string $KlapUserHash
     * @property ?int $KlapSequenz
     * @property string $LocalNonce
     * @property string $ServerNonce
     * @property string $Username
     * @property string $PwdHash
     * @property string $TokenUrl
     * @property string[] $ChildIDs
     * @property int $Version
     *
     * @method void RegisterAttributeArray(string $Name, array $Value, int $Size = 0)
     * @method array ReadAttributeArray(string $Name)
     * @method void WriteAttributeArray(string $Name, array $Value)
     * @method void UnregisterProfile(string $Name)
     * @method bool SendDebug(string $Message, mixed $Data, int $Format)
     * @method int FindIDForIdent(string $Ident)
     */
    class Device extends \IPSModuleStrict
    {
        use \Tapo\BufferHelper;
        use \Tapo\DebugHelper;
        use \Tapo\Semaphore;
        use \Tapo\VariableHelper;
        use \Tapo\VariableProfileHelper;
        use \Tapo\AttributeArrayHelper;
        use Crypt\Klap;
        use Crypt\AESSecurePassthrough;
        use Crypt\SSLAESSecurePassthrough;

        protected static $ModuleIdents = [];
        protected $TranslationCache = [];

        /**
         * Create
         *
         * @return void
         */
        public function Create(): void
        {
            //Never delete this line!
            parent::Create();
            $this->RegisterPropertyBoolean(\TpLink\Property::Open, false);
            $this->RegisterPropertyString(\TpLink\Property::Host, '');
            $this->RegisterPropertyString(\TpLink\Property::Mac, '');
            $this->RegisterPropertyString(\TpLink\Property::EncryptType, 'KLAP');
            $this->RegisterPropertyString(\TpLink\Property::Protocol, \TpLink\Crypt\HTTP);
            $this->RegisterPropertyString(\TpLink\Property::Username, '');
            $this->RegisterPropertyString(\TpLink\Property::Password, '');
            $this->RegisterPropertyInteger(\TpLink\Property::Interval, 5);
            $this->RegisterPropertyBoolean(\TpLink\Property::AutoRename, false);
            $this->RegisterTimer(\TpLink\Timer::RequestState, 0, 'TAPOSH_RequestState($_IPS[\'TARGET\']);');
            $this->terminalUUID = self::guidv4();
            $this->InitBuffers();
            $this->ChildIDs = [];
            $this->Version = 0;
        }

        /**
         * Migrate
         *
         * @param  string $JSONData
         * @return string
         */
        public function Migrate(string $JSONData): string
        {
            // Prüfe Version diese Modul-Instanz
            $j = json_decode($JSONData);
            if (isset($j->configuration->{\TpLink\Property::EncryptType})) {
                return $JSONData;
            }
            $this->LogMessage('Migrate Protocol and EncryptType', KL_NOTIFY);
            $j->configuration->{\TpLink\Property::EncryptType} = $j->configuration->{\TpLink\Property::Protocol};
            $j->configuration->{\TpLink\Property::Protocol} = \TpLink\Crypt\HTTP;
            return json_encode($j);
        }

        /**
         * ApplyChanges
         *
         * @return void
         */
        public function ApplyChanges(): void
        {
            //Never delete this line!
            parent::ApplyChanges();

            $this->UnregisterProfile(\TpLink\VariableProfile::RuntimeSeconds);
            $this->SetTimerInterval(\TpLink\Timer::RequestState, 0);
            $this->SetSummary($this->ReadPropertyString(\TpLink\Property::Host));
            $this->InitBuffers();
            if ($this->ReadPropertyBoolean(\TpLink\Property::Open)) {
                if ($this->ReadPropertyString(\TpLink\Property::Host) != '') {
                    if (!$this->Init()) {
                        $this->SetStatus(IS_EBASE + 1);
                    }
                    $this->SetTimerInterval(\TpLink\Timer::RequestState, $this->ReadPropertyInteger(\TpLink\Property::Interval) * 1000);
                    $this->FetchAppComponents();
                    return;
                }
            } else {
                $this->SetStatus(IS_INACTIVE);
            }
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
            if (!$this->ReadPropertyBoolean(\TpLink\Property::Open)) {
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                restore_error_handler();
                return;
            }
            $SendIdent = $Ident;
            if (substr($Ident, 0, 4) == 'Pos_') {
                $IdentParts = explode('_', substr($Ident, 4));
                $Values[\TpLink\Api\Result::DeviceID] = $this->ChildIDs[array_shift($IdentParts)];
                $SendIdent = implode('_', $IdentParts);
            }
            $Parts = explode('__', $SendIdent);
            if (count($Parts) > 1) {
                $ComponentClass = array_shift($Parts);
                if (class_exists('\\TpLink\\Components\\' . $ComponentClass)) {

                    $SendIdent = $Ident;
                    $SendValue = $Value;
                    /** @var \TpLink\Components\Component $Class */
                    $Class = \TpLink\Components\Component::getClass(lcfirst($ComponentClass));
                    if (method_exists($this, 'processSpecialWritePayload')) {
                        if ($this->processSpecialWritePayload($Class->getComponentName(), $Ident, $Value)) {
                            return;
                        }
                    }
                    if (isset($Class::$Variables[$SendIdent])) {
                        if (array_key_exists(\TpLink\SendFunction, $Class::$Variables[$SendIdent])) {
                            $SendValue = $Class::{$Class::$Variables[$SendIdent][\TpLink\SendFunction]}($Value);
                            if ($SendValue === null) {
                                return;
                            }
                        } else {
                            switch ($Class::$Variables[$SendIdent][\TpLink\IPSVarType]) {
                                case VARIABLETYPE_BOOLEAN:
                                    $SendValue = $Value ? 'on' : 'off';
                                    break;
                                case VARIABLETYPE_INTEGER:
                                    $SendValue = strval($Value);
                                    break;
                                case VARIABLETYPE_STRING:
                                    $SendValue = strval($Value);
                                    break;
                            }
                        }
                        $WriteRequest = $Class::getWriteRequest($Parts, $SendValue);
                        if (count($WriteRequest)) {
                            $Request = \TpLink\Api\Protocol::BuildMultipleRequest([$WriteRequest]);
                            $Response = $this->SendRequest($Request);
                            if ($Response !== null && isset($Response[\TpLink\Api\Result::Responses][0][\TpLink\Api\ErrorCode]) && $Response[\TpLink\Api\Result::Responses][0][\TpLink\Api\ErrorCode] == \TpLink\Api\ErrorCodes::Success) {
                                $this->SendDebug('Write Response', $Response, 0);
                                $this->SetValue($Ident, $Value);
                                return;
                            }
                            $ErrorCode = $Response[\TpLink\Api\Result::Responses][0][\TpLink\Api\ErrorCode] ?? \TpLink\Api\ErrorCodes::UnknownMethodError;
                            set_error_handler([$this, 'ModulErrorHandler']);
                            trigger_error($ErrorCode . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($ErrorCode)), E_USER_NOTICE);
                            restore_error_handler();
                            return;
                        }
                    }
                }
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($this->Translate('Invalid ident'), E_USER_NOTICE);
                restore_error_handler();
                return;
            }
            $AllIdents = self::GetModuleIdents();
            if (array_key_exists($SendIdent, $AllIdents)) {
                if ($AllIdents[$SendIdent][\TpLink\HasAction]) {
                    $Values[$SendIdent] = $Value;
                    if ($this->SendInfoVariables($Values)) {
                        $this->SetValue($Ident, $Value);
                    }
                }
                return;
            }
            set_error_handler([$this, 'ModulErrorHandler']);
            trigger_error($this->Translate('Invalid ident'), E_USER_NOTICE);
            restore_error_handler();
        }

        /**
         * GetConfigurationForm
         *
         * @return string
         */
        public function GetConfigurationForm(): string
        {
            return file_get_contents(__DIR__ . '/form.json');
        }

        /**
         * Translate
         *
         * @param  string $Text
         * @return string
         */
        public function Translate(string $Text): string
        {
            if (count($this->TranslationCache)) {
                $translation = $this->TranslationCache;
            } else {
                $this->TranslationCache = $translation = json_decode(file_get_contents(__DIR__ . '/locale.json'), true);
            }
            $language = IPS_GetSystemLanguage();
            $code = explode('_', $language)[0];
            if (isset($translation['translations'])) {
                if (isset($translation['translations'][$language])) {
                    if (isset($translation['translations'][$language][$Text])) {
                        return $translation['translations'][$language][$Text];
                    }
                } elseif (isset($translation['translations'][$code])) {
                    if (isset($translation['translations'][$code][$Text])) {
                        return $translation['translations'][$code][$Text];
                    }
                }
            }
            return $Text;
        }

        /**
         * RequestState
         *
         * @return bool
         */
        public function RequestState(): bool
        {
            $Result = $this->GetDeviceInfo();
            if (is_array($Result)) {
                $this->SetVariables($Result);
                return true;
            }
            return false;
        }

        /**
         * GetDeviceInfo
         *
         * @return false
         */
        public function GetDeviceInfo(): false|array
        {
            if ($this->Version == 3) {
                $Response = $this->SendMultipleRequest([
                    [
                        \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::GetDeviceInfo,
                        \TpLink\Api\Protocol::Params => [
                            'device_info'=> [
                                'name' => [
                                    'basic_info'
                                ]
                            ]
                        ]
                    ]
                ]);
                if (isset($Response[\TpLink\Api\MethodV3::GetDeviceInfo]['device_info']['basic_info'])) {
                    $Response = $Response[\TpLink\Api\MethodV3::GetDeviceInfo]['device_info']['basic_info'];
                    if (array_key_exists(\TpLink\Api\Result::DeviceAlias, $Response)) {
                        $Name = $Response[\TpLink\Api\Result::DeviceAlias];
                        if ($this->ReadPropertyBoolean(\TpLink\Property::AutoRename) && (IPS_GetName($this->InstanceID) != $Name) && ($Name != '')) {
                            IPS_SetName($this->InstanceID, $Name);
                        }
                    }
                }
                return $Response;
            }
            $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetDeviceInfo);
            $Response = $this->SendRequest($Request);
            if ($Response === null) {
                return false;
            }
            if (array_key_exists(\TpLink\Api\Result::Nickname, $Response)) {
                $Name = base64_decode($Response[\TpLink\Api\Result::Nickname]);
                if ($this->ReadPropertyBoolean(\TpLink\Property::AutoRename) && (IPS_GetName($this->InstanceID) != $Name) && ($Name != '')) {
                    IPS_SetName($this->InstanceID, $Name);
                }
            }
            return $Response;
        }

        /**
         * SendMultipleRequest
         *
         * @param  array $Requests
         * @return array
         */
        protected function SendMultipleRequest(array $Requests): array
        {
            $Request = \TpLink\Api\Protocol::BuildMultipleRequest(
                $Requests
            );
            $RequestMethods = array_column($Requests, \TpLink\Api\Protocol::Method);
            $Results = array_combine($RequestMethods, array_fill(0, count($RequestMethods), null));
            $Responses = $this->SendRequest($Request);
            $this->SendDebug('Request', $Request, 0);
            if ($Responses !== null && isset($Responses[\TpLink\Api\Result::Responses])) {
                $Responses = $Responses[\TpLink\Api\Result::Responses];
            } else {
                return $Results;
            }
            foreach ($Responses as $Response) {
                if (!isset($Response[\TpLink\Api\ErrorCode]) || $Response[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                    $ErrorCode = $Response[\TpLink\Api\ErrorCode] ?? \TpLink\Api\ErrorCodes::UnknownMethodError;
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($ErrorCode . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($ErrorCode)), E_USER_NOTICE);
                    restore_error_handler();
                    continue;
                }
                $Results[$Response[\TpLink\Api\Protocol::Method]] = $Response[\TpLink\Api\Result];
            }
            $this->SendDebug('Results', $Results, 0);
            return $Results;
        }

        /*
         * GetSysInfo
         *
         * @return void

        public function GetSysInfo()
        {
         */
        // ControlChild
        /*
        $ChildValue = [\TpLink\VariableIdentOnOff::device_on => false];
        $ChildRequest = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::SetDeviceInfo, '', $ChildValue);
        $Values = [
            'device_id'  => '8022B958FB2A8894109B291806AE20F12107CD8101',
            'requestData'=> $ChildRequest
        ];
        $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::ControlChild, $this->terminalUUID, $Values);
         */

        // MultipleRequest an Child
        /*
        $ChildRequest = ['requests' => [\TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetDeviceInfo)]];
        $Values = [
            'device_id'  => '8022B958FB2A8894109B291806AE20F12107CD8101',
            'requestData'=>
            \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::MultipleRequest,'', $ChildRequest)

        ];
        $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::ControlChild, $this->terminalUUID, $Values);
         */

        /*
        $ControlChildValues = [
           'device_id'  => '8022B958FB2A8894109B291806AE20F12107CD8101',
           'requestData'=> \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetDeviceInfo)
        ];
        $ControlChildRequest1 = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::ControlChild, '', $ControlChildValues);

        $ControlChildRequest2 = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetDeviceInfo, '', ['device_info'  => ['device_id'  => '8022B958FB2A8894109B291806AE20F12107CD8101']]);
        //$ControlChildRequest2 = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetDeviceInfo, '', ['device_info'  => ['name'=>['basic_info']]]);
        $Values = [
           'requests' => [
               $ControlChildRequest1,
               $ControlChildRequest2]
        ];
        $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::MultipleRequest, $this->terminalUUID, $Values);

        $Request = \TpLink\Api\Protocol::BuildRequest('delete_all_rules', $this->terminalUUID, ['id'=>'C1']);
        $Request = \TpLink\Api\Protocol::BuildRequest('reboot');
        //$Request = \TpLink\Api\Protocol::BuildRequest('set_device_info', $this->terminalUUID,  ['set_led_off'=> ['off'=>2]]);
        $Response = $this->SendRequest($Request);
        if ($Response === null) {
           return false;
        }*/
        //}

        /**
         * FetchAppComponents
         *
         * @return void
         */
        protected function FetchAppComponents(): void
        {
            $Response = $this->SendMultipleRequest([
                [
                    \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::GetAppComponentList,
                    \TpLink\Api\Protocol::Params => [
                        'app_component' => [
                            'name' => ['app_component_list']
                        ]
                    ]
                ],
                [
                    \TpLink\Api\Protocol::Method => \TpLink\Api\MethodV3::GetConnectionType,
                    \TpLink\Api\Protocol::Params => [
                        'network' => [
                            'get_connection_type' => new \stdClass()
                        ]
                    ]
                ]
            ]);
            if ($Response[\TpLink\Api\MethodV3::GetConnectionType] !== null) {
                $Result = $Response[\TpLink\Api\MethodV3::GetConnectionType];
                if (isset($Result['link_type'])) {
                    $this->RegisterVariableString('link_type', $this->Translate('Connection Type'), [], 0);
                    $this->SetValue('link_type', $this->Translate($Result['link_type']));
                }
                if (isset($Result['rssiValue'])) {
                    $this->RegisterVariableInteger('rssiValue', $this->Translate('RSSI Value'), [], 0);
                    $this->SetValue('rssiValue', (int) $Result['rssiValue']);
                }
            }
            if ($Response[\TpLink\Api\MethodV3::GetAppComponentList] !== null) {
                $ComponentData = $Response[\TpLink\Api\MethodV3::GetAppComponentList]['app_component'];
                if (isset($ComponentData[\TpLink\Api\Result::AppComponentList])) {
                    $this->ProcessAppComponents($ComponentData[\TpLink\Api\Result::AppComponentList]);
                }
            }
        }

        /**
         * ProcessAppComponents
         *
         * @param array $Components
         * @return void
         */
        protected function ProcessAppComponents(array $Components): void
        {
            $this->SendDebug('AppComponents', $Components, 0);

            foreach ($Components as $Component) {
                if (!\TpLink\Components\Component::isComponentNameValid($Component[\TpLink\Api\Result::ComponentName])) {
                    $this->SendDebug('Skip Component', $Component[\TpLink\Api\Result::ComponentName], 0);
                    continue;
                }
                /**
                 * @var \TpLink\Components\Component $Class
                 */
                $Class = \TpLink\Components\Component::getClass($Component[\TpLink\Api\Result::ComponentName]);
                $this->SendDebug('ComponentClass', $Class->getComponentName(), 0);
                $Responses = $this->SendMultipleRequest($Class::getReadRequest());
                foreach ($Responses as $Method => $Response) {
                    if ($Response === null) {
                        continue;
                    }
                    $this->SendDebug($Class->getComponentName() . ': Read Response', $Response, 0);
                    if (method_exists($this, 'processSpecialReadResponse')) {
                        $Variables = $this->processSpecialReadResponse($Method, $Response);
                        if (is_array($Variables)) {
                            $this->SendDebug('Variables:', $Variables, 0);
                            foreach ($Variables as $Ident =>$Variable) {
                                if (array_key_exists(\TpLink\IPSVarPresentationFunction, $Variable)) {
                                    $Presentation = $Class->{$Variable[\TpLink\IPSVarPresentationFunction]}();
                                } else {
                                    $Presentation = $this->TranslatePresentation($Variable[\TpLink\IPSVarPresentation]);
                                }
                                $this->MaintainVariable(
                                    $Ident,
                                    $this->Translate($Variable[\TpLink\IPSVarName]),
                                    $Variable[\TpLink\IPSVarType],
                                    $Presentation,
                                    0,
                                    true
                                );
                                if ($Variable[\TpLink\HasAction] ?? false) {
                                    $this->EnableAction($Ident);
                                }
                                if (isset($Variable['Value'])) {
                                    $this->SetValue($Ident, $Variable['Value']);
                                }
                            }
                            continue;
                        }
                    }
                    $Idents = $Class::processReadResponse($Response, $Class->getComponentName());
                    $this->SendDebug('Ident:', $Idents, 0);
                    foreach ($Idents as $Ident => $Value) {
                        if (!array_key_exists($Ident, $Class::$Variables)) {
                            continue;
                        }
                        $VarParams = $Class::$Variables[$Ident];
                        if (array_key_exists(\TpLink\ReceiveFunction, $VarParams)) {
                            $Value = $Class::{$VarParams[\TpLink\ReceiveFunction]}($Value);
                            if (is_null($Value)) {
                                continue;
                            }
                        } else {
                            switch ($VarParams[\TpLink\IPSVarType]) {
                                case VARIABLETYPE_BOOLEAN:
                                    $Value = $Value == 'on';
                                    break;
                                case VARIABLETYPE_INTEGER:
                                    $Value = intval($Value);
                                    break;
                                case VARIABLETYPE_STRING:
                                    $Value = strval($Value);
                                    break;
                            }
                        }
                        if (array_key_exists(\TpLink\IPSVarPresentationFunction, $VarParams)) {
                            $Presentation = $Class->{$VarParams[\TpLink\IPSVarPresentationFunction]}();
                        } else {
                            $Presentation = $this->TranslatePresentation($VarParams[\TpLink\IPSVarPresentation]);
                        }
                        $this->MaintainVariable(
                            $Ident,
                            $this->Translate($VarParams[\TpLink\IPSVarName]),
                            $VarParams[\TpLink\IPSVarType],
                            $Presentation,
                            0,
                            true
                        );
                        if ($VarParams[\TpLink\HasAction] ?? false) {
                            $this->EnableAction($Ident);
                        }
                        $this->SetValue($Ident, $Value);
                    }
                }
            }
        }

        /**
         * OverheatStatusToBool
         *
         * @param  array $Values
         * @return bool
         */
        protected function OverheatStatusToBool(array $Values): ?bool
        {
            if (!array_key_exists(\TpLink\VariableIdentOverheatedStatus::overheat_status, $Values)) {
                return null;
            }
            return $Values[\TpLink\VariableIdentOverheatedStatus::overheat_status] != 'normal';
        }

        /**
         * TranslatePresentation
         *
         * @param  array $Presentation
         * @return array
         */
        protected function TranslatePresentation(array $Presentation): array
        {

            if (isset($Presentation['PREFIX'])) {
                $Presentation['PREFIX'] = $this->Translate($Presentation['PREFIX']);
            }
            if (isset($Presentation['SUFFIX'])) {
                $Presentation['SUFFIX'] = $this->Translate($Presentation['SUFFIX']);
            }
            if (isset($Presentation['OPTIONS'])) {
                $Options = $Presentation['OPTIONS'];
                foreach ($Options as &$Option) {
                    $Option['Caption'] = $this->Translate($Option['Caption']);
                }
                $Presentation['OPTIONS'] = json_encode($Options);
            }
            if (isset($Presentation['INTERVALS'])) {
                $Intervals = $Presentation['INTERVALS'];
                foreach ($Intervals as &$Interval) {
                    if (isset($Interval['ConstantValue'])) {
                        $Interval['ConstantValue'] = $this->Translate($Interval['ConstantValue']);
                    }
                    if (isset($Interval['PrefixValue'])) {
                        $Interval['PrefixValue'] = $this->Translate($Interval['PrefixValue']);
                    }
                    if (isset($Interval['SuffixValue'])) {
                        $Interval['SuffixValue'] = $this->Translate($Interval['SuffixValue']);
                    }
                }
                $Presentation['INTERVALS'] = json_encode($Intervals);
            }
            return $Presentation;
        }

        /**
         * SetVariables
         *
         * @param  array $Values
         * @return void
         */
        protected function SetVariables(array $Values): void
        {
            $NamePrefix = '';
            $IdentPrefix = '';
            if (array_key_exists(\TpLink\Api\Result::Position, $Values)) {
                $IdentPrefix = 'Pos_' . $Values[\TpLink\Api\Result::Position] . '_';
                if (array_key_exists(\TpLink\Api\Result::Nickname, $Values)) {
                    $NamePrefix = base64_decode($Values[\TpLink\Api\Result::Nickname]) . ' - ';
                }
            }
            foreach (self::GetModuleIdents() as $Ident => $VarParams) {
                if (array_key_exists(\TpLink\ReceiveFunction, $VarParams)) {
                    $Values[$Ident] = $this->{$VarParams[\TpLink\ReceiveFunction]}($Values);
                    if (is_null($Values[$Ident])) {
                        continue;
                    }
                } else {
                    if (!array_key_exists($Ident, $Values)) {
                        continue;
                    }
                }
                if (array_key_exists(\TpLink\IPSVarPresentationFunction, $VarParams)) {
                    $Presentation = $this->{$VarParams[\TpLink\IPSVarPresentationFunction]}();
                } else {
                    $Presentation = $this->TranslatePresentation($VarParams[\TpLink\IPSVarPresentation]);
                }
                $this->MaintainVariable(
                    $IdentPrefix . $Ident,
                    $NamePrefix . $this->Translate($VarParams[\TpLink\IPSVarName]),
                    $VarParams[\TpLink\IPSVarType],
                    $Presentation,
                    0,
                    true
                );
                if ($VarParams[\TpLink\HasAction]) {
                    $this->EnableAction($IdentPrefix . $Ident);
                }
                $this->SetValue($IdentPrefix . $Ident, $Values[$Ident]);
            }
        }

        /**
         * SetStatus
         *
         * @param  mixed $Status
         * @return bool
         */
        protected function SetStatus($Status): bool
        {
            if ($Status != IS_ACTIVE) {
                $this->InitBuffers();
            }
            if ($this->GetStatus() != $Status) {
                parent::SetStatus($Status);

                if ($Status == IS_ACTIVE) {
                    $this->RequestState();
                }
            }
            return true;
        }

        /**
         * SendInfoVariables
         *
         * @param  array $Values
         * @return bool
         */
        protected function SendInfoVariables(array $Values): bool
        {
            $SendValues = [];
            if (array_key_exists(\TpLink\api\Result::DeviceID, $Values)) {
                $SendValues[\TpLink\api\Result::DeviceID] = $Values[\TpLink\api\Result::DeviceID];
            }
            $AllIdents = self::GetModuleIdents();
            $NoError = false;
            foreach ($Values as $Ident => $Value) {
                if (!array_key_exists($Ident, $AllIdents)) {
                    continue;
                }
                if (array_key_exists(\TpLink\SendFunction, $AllIdents[$Ident])) {
                    $ConvertResult = $this->{$AllIdents[$Ident][\TpLink\SendFunction]}($Value);
                    if (is_bool($ConvertResult)) {
                        $NoError = true;
                    } else {
                        if (count($ConvertResult)) {
                            $SendValues = array_merge($SendValues, $ConvertResult);
                        }
                    }
                    continue;
                }
                $SendValues[$Ident] = $Value;
            }
            if (!count($SendValues)) {
                if (!$NoError) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Invalid ident'), E_USER_NOTICE);
                    restore_error_handler();
                }
                return false;
            }
            return $this->SetDeviceInfo($SendValues);
        }

        /**
         * SetDeviceInfo
         *
         * @param  array $Values
         * @return bool
         */
        protected function SetDeviceInfo(array $Values): bool
        {
            if (array_key_exists(\TpLink\api\Result::DeviceID, $Values)) {
                $ChildID = $Values[\TpLink\api\Result::DeviceID];
                unset($Values[\TpLink\api\Result::DeviceID]);
                $ChildRequestValues = [
                    'device_id'  => $ChildID,
                    'requestData'=> \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::SetDeviceInfo, '', $Values)
                ];
                $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::ControlChild, $this->terminalUUID, $ChildRequestValues);
            } else {
                $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::SetDeviceInfo, $this->terminalUUID, $Values);
            }
            $Response = $this->SendRequest($Request);
            if ($Response === null) {
                return false;
            }
            if (isset($ChildRequestValues)) {
                if ($Response[\TpLink\Api\Result::ResponseData][\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($Response[\TpLink\Api\ErrorCode] . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($Response[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
                    restore_error_handler();
                    return false;
                }
            }
            return true;
        }

        /**
         * SendRequest
         *
         * @param  array $TapoRequest
         * @return null|array
         */
        protected function SendRequest(array $TapoRequest): ?array
        {
            $Request = json_encode($TapoRequest);
            $this->SendDebug(__FUNCTION__, $Request, 0);
            if ($this->GetStatus() != IS_ACTIVE) {
                if ($this->ReadPropertyBoolean(\TpLink\Property::Open)) {
                    if (!$this->Init()) {
                        set_error_handler([$this, 'ModulErrorHandler']);
                        trigger_error($this->Translate('Error on reconnect'), E_USER_NOTICE);
                        restore_error_handler();
                        $this->SetStatus(IS_EBASE + 1);
                        return null;
                    }
                } else {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    restore_error_handler();
                    return null;
                }
            }
            $Result = null;
            $JSON = '';
            if ($this->KlapRemoteSeed !== '') {
                $JSON = $this->KlapEncryptedRequest($Request);
            }
            if ($this->TokenUrl !== '') {
                if ($this->PwdHash != '') {
                    if ($this->TpLinkCipherKey != '' && $this->TpLinkCipherIV != '') {
                        $JSON = $this->SSLAESEncryptedRequest($Request);
                    } else {
                        $JSON = $this->LessSecureRequest($Request);
                    }

                } else {
                    $JSON = $this->AESEncryptedRequest($Request);
                }
            }
            if ($JSON != '') {
                $Result = json_decode($JSON, true);
                if ($Result[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($Result[\TpLink\Api\ErrorCode] . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($Result[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
                    restore_error_handler();
                    return null;
                }
                if (array_key_exists(\TpLink\Api\Result, $Result)) {
                    $Result = $Result[\TpLink\Api\Result];
                }
            } else {
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($this->Translate('No response'), E_USER_NOTICE);
                restore_error_handler();
            }
            return $Result;
        }

        /**
         * CurlDebug
         *
         * @param  int $HttpCode
         * @return void
         */
        protected function CurlDebug(int $HttpCode): void
        {
            switch ($HttpCode) {
                case 0:
                    $this->SendDebug('Not connected', '', 0);
                    break;
                case 400:
                    $this->SendDebug('Bad Request', $HttpCode, 0);
                    break;
                case 401:
                    $this->SendDebug('Unauthorized Error', $HttpCode, 0);
                    break;
                case 404:
                    $this->SendDebug('Not Found Error', $HttpCode, 0);
                    break;
            }
        }

        /**
         * ModulErrorHandler
         *
         * @param  int $errno
         * @param  string $errstr
         * @return bool
         */
        protected function ModulErrorHandler(int $errno, string $errstr): bool
        {
            echo $errstr . PHP_EOL;
            return true;
        }

        /**
         * GetModuleIdents
         *
         * @return array
         */
        private static function GetModuleIdents(): array
        {
            $AllIdents = [];
            foreach (static::$ModuleIdents as $VariableIdentClassName) {
                /** @var VariableIdent $VariableIdentClassName */
                $AllIdents = array_merge($AllIdents, $VariableIdentClassName::$Variables);
            }
            return $AllIdents;
        }

        /**
         * InitBuffers
         *
         * @return void
         */
        private function InitBuffers(): void
        {
            $this->cookie = '';
            $this->TpLinkCipherKey = '';
            $this->TpLinkCipherIV = '';
            $this->LocalNonce = '';
            $this->ServerNonce = '';
            $this->Username = '';
            $this->PwdHash = '';
            $this->TokenUrl = '';
            $this->KlapLocalSeed = '';
            $this->KlapRemoteSeed = '';
            $this->KlapUserHash = '';
            $this->KlapSequenz = null;
        }

        /**
         * Init
         *
         * @return bool
         */
        private function Init(): bool
        {
            switch ($this->ReadPropertyString(\TpLink\Property::EncryptType)) {
                case 'AES':
                    switch ($this->ReadPropertyString(\TpLink\Property::Protocol)) {
                        case \TpLink\Crypt\HTTPS:
                            $this->Version = 3;
                            if ($this->InitSSLAES()) {
                                $this->SetStatus(IS_ACTIVE);
                                return true;
                            }
                            return false;
                        case \TpLink\Crypt\HTTP:
                            $this->Version = 1;
                            $Result = $this->HandshakeAES();
                            if ($Result === true) {
                                if ($this->LoginAES()) {
                                    $this->SetStatus(IS_ACTIVE);
                                    return true;
                                }
                                return false;
                            }
                            if ($Result === false) {
                                $Result = -1;
                            }
                            set_error_handler([$this, 'ModulErrorHandler']);
                            trigger_error($this->Translate(\TpLink\Api\ErrorCodes::getText($Result)), E_USER_NOTICE);
                            restore_error_handler();
                            return false;
                    }
                    break;
                case 'KLAP':
                    $this->Version = 2;
                    if ($this->InitKlap()) {
                        if ($this->HandshakeKlap()) {
                            $this->SetStatus(IS_ACTIVE);
                            return true;
                        }
                    }
                    return false;
            }
            set_error_handler([$this, 'ModulErrorHandler']);
            trigger_error($this->Translate(\TpLink\Api\ErrorCodes::getText(1003)), E_USER_NOTICE);
            restore_error_handler();
            return false;
        }

        /**
         * CurlRequest
         *
         * @param  string $Url
         * @param  string $Payload
         * @param  bool $noError
         * @return false
         */
        private function CurlRequest(string $Url, string $Payload, bool $noError = false, array $Headers = []): false|string
        {
            $this->SendDebug('Curl Request', $Url, 0);
            $Headers = array_merge([
                'Content-Type: application/json; charset=UTF-8',
                'requestByApp: true',
                'Accept: application/json',
                'Accept-Encoding: gzip, deflate',
                'User-Agent: Tapo CameraClient Android',
            ], $Headers);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_URL, $Url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $Payload);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 4000);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
            curl_setopt($ch, CURLOPT_COOKIELIST, $this->cookie);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            $Result = curl_exec($ch);
            $HttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $Cookie = curl_getinfo($ch, CURLINFO_COOKIELIST);
            $this->CurlDebug($HttpCode);
            if ($HttpCode == 200) {
                $this->cookie = (is_array($Cookie)) ? array_shift($Cookie) : '';
                return $Result;
            }
            $this->SendDebug('CURL INFO', curl_getinfo($ch), 0);

            if (($HttpCode == 0) && (!$noError)) {
                $this->SetStatus(IS_EBASE + 1);
            }
            return false;
        }

        /**
         * guidv4
         *
         * @param  mixed $data
         * @return string
         */
        private static function guidv4($data = null): string
        {
            // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
            $data = $data ?? random_bytes(16);
            assert(strlen($data) == 16);
            // Set version to 0100
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            // Set bits 6-7 to 10
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            // Output the 36 character UUID.
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
    }

    trait EnergyUsage
    {
        /**
         * RequestState
         *
         * @return bool
         */
        public function RequestState(): bool
        {
            if (parent::RequestState()) {
                return $this->GetEnergyUsage();
            }
            return false;
        }

        /**
         * GetEnergyUsage
         *
         * @return false
         */
        public function GetEnergyUsage(): bool
        {
            $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetEnergyUsage);
            $Result = $this->SendRequest($Request);
            if ($Result === null) {
                return false;
            }
            $this->SetValue(\TpLink\VariableIdentEnergySocket::today_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::today_runtime] * 60);
            $this->SetValue(\TpLink\VariableIdentEnergySocket::month_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::month_runtime] * 60);
            $this->SetValue(\TpLink\VariableIdentEnergySocket::today_energy, $Result[\TpLink\VariableIdentEnergySocket::today_energy]);
            $this->SetValue(\TpLink\VariableIdentEnergySocket::month_energy, $Result[\TpLink\VariableIdentEnergySocket::month_energy]);
            $this->SetValue(\TpLink\VariableIdentEnergySocket::current_power, ($Result[\TpLink\VariableIdentEnergySocket::current_power] / 1000));
            return true;
        }
    }

    trait EnergyUsageChilds
    {
        /**
         * RequestState
         *
         * @return bool
         */
        public function RequestState(): bool
        {
            if (parent::RequestState()) {
                foreach ($this->ChildIDs as $ChildID) {
                    $Values = [
                        'device_id'  => $ChildID,
                        'requestData'=> \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetDeviceInfo)
                    ];
                    $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::ControlChild, $this->terminalUUID, $Values);
                    $Response = $this->SendRequest($Request);
                    if ($Response === null) {
                        return false;
                    }
                    $this->SetVariables($Response[\TpLink\Api\Result::ResponseData][\TpLink\Api\Result]);
                }
                return $this->GetEnergyUsageChilds();
            }
            return false;

        }

        /**
         * GetEnergyUsageChilds
         *
         * @return bool
         */
        protected function GetEnergyUsageChilds(): bool
        {
            foreach ($this->ChildIDs as $Position => $ChildID) {
                $Values = [
                    'device_id'  => $ChildID,
                    'requestData'=> \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetEnergyUsage)
                ];
                $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::ControlChild, $this->terminalUUID, $Values);
                $Response = $this->SendRequest($Request);
                if ($Response === null) {
                    return false;
                }
                $Result = $Response[\TpLink\Api\Result::ResponseData][\TpLink\Api\Result];
                $IdentPrefix = 'Pos_' . $Position . '_';
                $this->SetValue($IdentPrefix . \TpLink\VariableIdentEnergySocket::today_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::today_runtime] * 60);
                $this->SetValue($IdentPrefix . \TpLink\VariableIdentEnergySocket::month_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::month_runtime] * 60);
                $this->SetValue($IdentPrefix . \TpLink\VariableIdentEnergySocket::today_energy, $Result[\TpLink\VariableIdentEnergySocket::today_energy]);
                $this->SetValue($IdentPrefix . \TpLink\VariableIdentEnergySocket::month_energy, $Result[\TpLink\VariableIdentEnergySocket::month_energy]);
            }
            return true;
        }
    }
}

