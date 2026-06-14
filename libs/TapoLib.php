<?php

declare(strict_types=1);

namespace {
    require_once 'VariableIdent.php';
}

/**
 * TapoLib
 * Enthält Klassen welche die API und Geräte Fähigkeiten abbilden.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.70
 */

namespace TpLink\Api
{
    const ErrorCode = 'error_code';
    const ErrCode = 'err_code';
    const InnerErrorCode = 'code';
    const Result = 'result';

    class Method
    {
        // Connection
        public const string Handshake = 'handshake';
        public const string Login = 'login';
        public const string LoginDevice = 'login_device';

        public const MultipleRequest = 'multipleRequest';

        // Get/Set Values
        public const GetDeviceInfo = 'get_device_info';
        public const SetDeviceInfo = 'set_device_info';

        // Get/Set Time
        public const GetDeviceTime = 'get_device_time';
        public const SetDeviceTime = 'set_device_time';

        // Get Energy Values
        public const GetCurrentPower = 'get_current_power';
        public const GetEnergyUsage = 'get_energy_usage';

        // not used (now)
        public const GetDeviceUsage = 'get_device_usage';
        public const SetLightingEffect = 'set_lighting_effect';
        public const GetLightingEffect = 'get_lighting_effect';

        // not working :(
        //public const Reboot = 'reboot';
        //public const SetRelayState = 'set_relay_state'; // 'state' => int
        //public const SetLedOff = 'set_led_off'; //array 'off'=>int
        //public const GetLightState = 'get_light_state';

        // Control Child
        public const GetChildDeviceList = 'get_child_device_list';
        public const GetChildDeviceComponentList = 'get_child_device_component_list';
        public const ControlChild = 'control_child';

        //get_child_device_component_list

        public const CountdownRule = 'add_countdown_rule'; // todo wie löschen?
    }

    class MethodV3
    {
        public const GetDeviceInfo = 'getDeviceInfo';
        public const GetAppComponentList = 'getAppComponentList';
        public const GetConnectionType = 'getConnectionType';
        public const GetChildDeviceList = 'getChildDeviceList';
        public const GetChildDeviceComponentList = 'getChildDeviceComponentList';
        public const ControlChild = 'controlChild';
        // Device
        public const GetSdCardStatus = 'getSdCardStatus';
        public const GetCircularRecordingConfig = 'getCircularRecordingConfig';
        //public const GetClockStatus = 'getClockStatus';
        //public const GetDstRule = 'getDstRule';
        //public const GetTimezone = 'getTimezone';
        //public const GetNightVisionCapability = 'getNightVisionCapability';

        // Detection Methods - Get
        public const GetDetectionConfig = 'getDetectionConfig';
        //public const GetDetectionRegion = 'getDetectionRegion';
        public const GetPersonDetectionConfig = 'getPersonDetectionConfig';
        public const GetBCDConfig = 'getBCDConfig';
        public const GetVehicleDetectionConfig = 'getVehicleDetectionConfig';
        public const GetPetDetectionConfig = 'getPetDetectionConfig';
        public const GetBarkDetectionConfig = 'getBarkDetectionConfig';
        public const GetMeowDetectionConfig = 'getMeowDetectionConfig';
        public const GetGlassDetectionConfig = 'getGlassDetectionConfig';
        public const GetTamperDetectionConfig = 'getTamperDetectionConfig';
        //public const GetLinecrossingDetectionConfig = 'getLinecrossingDetectionConfig';
        //public const GetLinecrossingDetectionRegion = 'getLinecrossingDetectionRegion';

        // Detection Methods - Set
        public const SetDetectionConfig = 'setDetectionConfig';
        public const SetPersonDetectionConfig = 'setPersonDetectionConfig';
        public const SetBCDConfig = 'setBCDConfig';
        public const SetVehicleDetectionConfig = 'setVehicleDetectionConfig';
        public const SetPetDetectionConfig = 'setPetDetectionConfig';
        public const SetBarkDetectionConfig = 'setBarkDetectionConfig';
        public const SetMeowDetectionConfig = 'setMeowDetectionConfig';
        public const SetGlassDetectionConfig = 'setGlassDetectionConfig';
        public const SetTamperDetectionConfig = 'setTamperDetectionConfig';
        //public const SetLinecrossingDetectionConfig = 'setLinecrossingDetectionConfig';

        // Track & Alert Methods
        public const GetTargetTrackConfig = 'getTargetTrackConfig';
        public const SetTargetTrackConfig = 'setTargetTrackConfig';
        //public const GetAlertTypeList = 'getAlertTypeList';
        public const GetAlertConfig = 'getAlertConfig';
        public const SetAlertConfig = 'setAlertConfig';
        //public const GetAlertPlan = 'getAlertPlan';
        //public const SetAlertPlan = 'setAlertPlan';
        public const GetAlertEventType = 'getAlertEventType';
        public const SetAlertEventType = 'setAlertEventType';
        public const GetMsgPushConfig = 'getMsgPushConfig';
        public const SetMsgPushConfig = 'setMsgPushConfig';
        //public const GetMsgPushPlan = 'getMsgPushPlan';
        //public const SetMsgPushPlan = 'setMsgPushPlan';
        public const GetMsgPushEventList = 'getMsgPushEventList';
        public const SetMsgPushEventList = 'setMsgPushEventList';

        // LED & LensMask Methods
        public const GetLedStatus = 'getLedStatus';
        public const SetLedStatus = 'setLedStatus';
        public const GetLensMaskConfig = 'getLensMaskConfig';
        public const SetLensMaskConfig = 'setLensMaskConfig';

        // Battery Methods
        public const GetBatteryStatus = 'getBatteryStatus';
        public const SetBatteryConfig = 'setBatteryConfig';

        // PanTilt Methods
        //public const SetPresetConfig = 'setPresetConfig';
        //public const SetPanTilt = 'setPanTilt';
        public const MotorMoveToPreset = 'motorMoveToPreset';

        //??? public const GetLastAlarmInfo = 'getLastAlarmInfo';
        public const GetPresetConfig = 'getPresetConfig';
        //public const GetFirmwareUpdateStatus = 'getFirmwareUpdateStatus';
        //public const GetMediaEncrypt = 'getMediaEncrypt';
        public const GetSirenConfig = 'getSirenConfig';
        public const SetSirenConfig = 'setSirenConfig';
        public const GetSirenStatus = 'getSirenStatus';
        public const SetSirenStatus = 'setSirenStatus';
        public const GetSirenTypeList = 'getSirenTypeList';

        //public const GetNightVisionModeConfig = 'getNightVisionModeConfig';
        //public const GetWhitelampConfig = 'getWhitelampConfig';
        //public const GetRecordPlan = 'getRecordPlan';
        //public const GetAudioConfig = 'getAudioConfig';
        //public const GetFirmwareAutoUpgradeConfig = 'getFirmwareAutoUpgradeConfig';
        //public const GetVideoQualities = 'getVideoQualities';
        //public const GetVideoCapability = 'getVideoCapability';
    }

    class Param
    {
        public const Username = 'username';
        public const Password = 'password';
        public const DigestPassword = 'digest_passwd';
        public const CNonce = 'cnonce';
        public const EncryptType = 'encrypt_type';
        public const Hashed = 'hashed';

    }

    class Result
    {
        public const Nickname = 'nickname';
        public const Response = 'response';
        public const Responses = 'responses';
        public const EncryptedKey = 'key';
        public const Ip = 'ip';
        public const Mac = 'mac';
        public const DeviceType = 'device_type';
        public const DeviceMode = 'device_mode';
        public const DeviceAlias = 'device_alias';
        public const Type = 'type';
        public const DeviceModel = 'device_model';
        public const Model = 'model';
        public const DeviceID = 'device_id';
        public const MGT = 'mgt_encrypt_schm';
        public const EncryptType = 'encrypt_type';
        public const Protocol = 'is_support_https';
        public const ChildList = 'child_device_list';
        public const Position = 'position';
        public const SlotNumber = 'slot_number';
        public const ResponseData = 'responseData';
        public const Category = 'category';
        public const Data = 'data';
        public const Code = 'code';
        public const Nonce = 'nonce';
        public const SecLeft = 'sec_left';
        public const DeviceConfirm = 'device_confirm';

        // App Component Results
        public const AppComponentList = 'app_component_list';
        public const ComponentName = 'name';
        public const ComponentNeedsAuth = 'needs_auth';
    }

    class DeviceMode
    {
        public const Motor = 'motor';
        public const Switch = 'switch';
    }
    class ErrorCodes
    {
        public const NotReachable = -1;
        public const Success = 0;
        public const TransportNotAvailableError = 1002;
        public const InvalidProtocol = 1003;
        public const SessionTimeout = 9999;
        public const ProtocolFormatError = 40210;
        public const InvalidParams = -1001;
        public const UnknownMethodError = -1002;
        public const JsonDecodeError = -1003;
        public const JsonEncodeError = -1004;
        public const AesDecodeError = -1005;
        public const ParamsError = -1008;
        public const InvalidPublicKeyLength = -1010;
        public const LoginError = -1501;
        public const RuleAlreadySet = -1901;
        public const InvalidArguments2 = -40106;
        public const InvalidArguments = -40209;
        public const SessionExpired = -40401;
        public const DeviceBlocked = -40404;
        public const OutOfLimit = -40406;
        public const BadUsername = -40411;
        public const InvalidNonce = -40413;
        public const InvalidUsername = -60502;

        public static $ErrorCodes = [
            self::NotReachable                => 'Not reachable',
            self::Success                     => 'Success',
            self::InvalidPublicKeyLength      => 'Invalid Public Key Length',
            self::LoginError                  => 'Login error',
            self::TransportNotAvailableError  => 'Transport not available',
            self::InvalidProtocol             => 'Invalid Protocol',
            self::ProtocolFormatError         => 'Protocol Format Error',
            self::InvalidParams               => 'Invalid Params',
            self::UnknownMethodError          => 'Unknown Method',
            self::JsonDecodeError             => 'JSON decode error',
            self::JsonEncodeError             => 'JSON encode error',
            self::AesDecodeError              => 'AES decode error',
            self::ParamsError                 => 'Params error',
            self::RuleAlreadySet              => 'Rule already set',
            self::InvalidArguments2           => 'Invalid arguments',
            self::InvalidNonce                => 'Invalid nonce',
            self::InvalidArguments            => 'Invalid arguments',
            self::SessionExpired              => 'Session expired',
            self::BadUsername                 => 'Bad username',
            self::InvalidUsername             => 'Invalid username',
            self::DeviceBlocked               => 'Device blocked for ',
            self::OutOfLimit                  => 'Out of limit',
            self::SessionTimeout              => 'Session Timeout'
        ];
        public static function getText(int $ErrorCode): string
        {
            return static::$ErrorCodes[$ErrorCode] ?? 'unknown error code';
        }
    }
    class Protocol
    {
        public const Method = 'method';
        public const Params = 'params';
        private const ParamHandshakeKey = 'key';
        private const DiscoveryKey = 'rsa_key';
        //private const requestTimeMils = 'requestTimeMils';
        private const requestTimeMils = 'request_time_milis';
        private const TerminalUUID = 'terminalUUID';

        public static function BuildHandshakeRequest(string $publicKey): string
        {
            return json_encode([
                self::Method=> Method::Handshake,
                self::Params=> [
                    self::ParamHandshakeKey => mb_convert_encoding($publicKey, 'ISO-8859-1', 'UTF-8')
                ],
                self::requestTimeMils => 0

            ]);
        }

        public static function BuildMultipleRequest(array $Requests): array
        {
            return [
                self::Method => Method::MultipleRequest,
                self::Params => [
                    'requests' => $Requests
                ],
                self::requestTimeMils => (round(time() * 1000))
            ];
        }
        public static function BuildRequest(string $Method, string $TerminalUUID = '', array $Params = []): array
        {
            $Request = [
                self::Method          => $Method,
            ];
            if ($TerminalUUID) {
                $Request[self::TerminalUUID] = $TerminalUUID;
            }
            if (count($Params)) {
                $Request[self::Params] = $Params;
                $Request[self::requestTimeMils] = (round(time() * 1000));
            }

            return $Request;
        }

        public static function BuildDiscoveryRequest(string $publicKey): string
        {
            return json_encode([
                self::Params=> [
                    self::DiscoveryKey=> mb_convert_encoding($publicKey, 'ISO-8859-1', 'UTF-8')
                ]
            ]);
        }
    }
}

namespace TpLink\Components
{
    class Component
    {
        // Device
        protected const SdCard = 'sdCard';
        //protected const Timezone = 'timezone';
        // Detection Components
        protected const Detection = 'detection';
        //protected const DetectionRegion = 'detectionRegion';
        protected const PersonDetection = 'personDetection';
        protected const BabyCryDetection = 'babyCryDetection';
        protected const VehicleDetection = 'vehicleDetection';
        protected const SoundDetection = 'soundDetection';
        protected const PetDetection = 'petDetection';
        protected const BarkDetection = 'barkDetection';
        protected const MeowDetection = 'meowDetection';
        protected const GlassDetection = 'glassDetection';
        protected const TamperDetection = 'tamperDetection';
        //protected const LinecrossingDetection = 'linecrossingDetection';

        // Track & Alert Components
        protected const TargetTrack = 'targetTrack';
        protected const Alert = 'alert';
        protected const MsgPush = 'msgPush';

        // SmartCam Components
        protected const RecordDownload = 'recordDownload';
        protected const Led = 'led';
        protected const LensMask = 'lensMask';
        protected const Battery = 'battery';
        protected const Ptz = 'ptz';
        protected const Siren = 'siren';
        public static $Variables = [];

        protected static $ReadRequest = [];
        protected static $WriteRequest = [];
        protected static $Classes = [
            self::SdCard,
            //self::Timezone,
            self::Detection,
            //self::DetectionRegion,
            self::PersonDetection,
            self::BabyCryDetection,
            self::VehicleDetection,
            self::SoundDetection,
            self::PetDetection,
            self::BarkDetection,
            self::MeowDetection,
            self::GlassDetection,
            self::TamperDetection,
            //self::LinecrossingDetection,
            self::TargetTrack,
            self::Alert,
            self::MsgPush,
            self::Led,
            self::LensMask,
            self::Battery,
            self::Ptz,
            self::Siren,
        ];
        public static function isComponentNameValid(string $ComponentName): bool
        {
            return in_array($ComponentName, self::$Classes);
        }
        public static function getClass(string $ComponentName): object
        {
            if (in_array($ComponentName, self::$Classes)) {
                $ClassName = __NAMESPACE__ . '\\' . ucwords($ComponentName);
                if (class_exists($ClassName)) {
                    return new $ClassName();
                }
            }
            throw new \InvalidArgumentException("Class not found for component: $ComponentName");
        }
        public function getComponentName(): string
        {
            $reflect = new \ReflectionClass($this);
            return ucwords($reflect->getShortName());
        }

        public static function getReadRequest(): array
        {
            return isset(static::$ReadRequest[\TpLink\Api\Protocol::Method]) ? [static::$ReadRequest] : static::$ReadRequest;
        }
        public static function getWriteRequest(array $ModuleIndexes, bool|int|float|string|array $ModuleValue): array
        {
            if (empty(static::$WriteRequest)) {
                return static::$WriteRequest;
            }
            return array_merge(static::$WriteRequest, [
                \TpLink\Api\Protocol::Params => self::getWriteRequestParams($ModuleIndexes, $ModuleValue)
            ]);
        }

        public static function processReadResponse(array $Response, string $Prefix = ''): array
        {
            $Idents = [];
            foreach ($Response as $key => $Value) {
                $NewKey = $Prefix !== '' ? $Prefix . '__' . $key : $key;

                if (is_array($Value)) {
                    $Idents = array_merge($Idents, self::processReadResponse($Value, $NewKey));
                } else {
                    $Idents[$NewKey] = $Value;
                }
            }
            return $Idents;
        }

        protected static function getWriteRequestParams(array $ModuleIndexes, bool|int|float|string|array $ModuleValue): array
        {
            $WriteRequestParams = $ModuleValue;
            foreach (array_reverse($ModuleIndexes) as $Index) {
                $WriteRequestParams = [$Index => $WriteRequestParams];
            }
            return $WriteRequestParams;
        }

    }
    class SdCard extends Component
    {
        public static $Variables = [
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__status' => [
                \TpLink\IPSVarName        => 'SD-Card state',
                \TpLink\IPSVarType        => VARIABLETYPE_STRING,
                \TpLink\IPSVarPresentation=> []
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__detect_status' => [
                \TpLink\IPSVarName        => 'SD-Card detected state',
                \TpLink\IPSVarType        => VARIABLETYPE_STRING,
                \TpLink\IPSVarPresentation=> []
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__write_protect' => [
                \TpLink\IPSVarName        => 'SD-Card write protect',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> []
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__percent' => [
                \TpLink\IPSVarName        => 'SD-Card fill level',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'COLOR'                       => -1,
                    'DECIMAL_SEPARATOR'           => '',
                    'DIGITS'                      => 0,
                    'ICON'                        => '',
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MAX'                         => 100,
                    'MIN'                         => 0,
                    'MULTILINE'                   => false,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 0,
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__type' => [
                \TpLink\IPSVarName        => 'SD-Card type',
                \TpLink\IPSVarType        => VARIABLETYPE_STRING,
                \TpLink\IPSVarPresentation=> []
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__record_duration' => [
                \TpLink\IPSVarName        => 'SD-Card recording duration',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> []
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__record_free_duration' => [
                \TpLink\IPSVarName        => 'SD-Card remaining recording time',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> []
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__record_start_time' => [
                \TpLink\IPSVarName        => 'SD-Card recording start time',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'PRESENTATION'    => VARIABLE_PRESENTATION_DATE_TIME,
                    'MONTH_TEXT'      => false,
                    'DAY_OF_THE_WEEK' => false,
                    'DATE'            => 1,
                    'TIME'            => 1
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__loop_record_status' => [
                \TpLink\IPSVarName        => 'SD-Card loop recording state',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> []
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__total_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card total space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__free_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card free space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__video_total_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card video total space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__video_free_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card video free space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__picture_total_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card picture total space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__picture_free_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card picture free space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__crossline_total_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card linecrossing total space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__crossline_free_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card linecrossing free space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__msg_push_total_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card pushmessage total space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ],
            'SdCard__harddisk_manage__hd_info__0__hd_info_1__msg_push_free_space_accurate' => [
                \TpLink\IPSVarName        => 'SD-Card pushmessage free space',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    'COLOR'            => -1,
                    'ICON'             => '',
                    'SUFFIX'           => ' MB',
                    'PRESENTATION'     => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'USAGE_TYPE'       => 0,
                    'PERCENTAGE'       => false,
                    'DIGITS'           => 2,
                    'INTERVALS_ACTIVE' => true,
                    'INTERVALS'        => [
                        [
                            'IntervalMinValue' => 0,
                            'IntervalMaxValue' => 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' Byte',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 0,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024,
                            'IntervalMaxValue' => 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' KB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024,
                            'IntervalMaxValue' => 1024 * 1024 * 1024,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' MB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                        [
                            'IntervalMinValue' => 1024 * 1024 * 1024,
                            'IntervalMaxValue' => PHP_FLOAT_MAX,
                            'ConstantActive'   => false,
                            'ConstantValue'    => '',
                            'ConversionFactor' => 1024 * 1024 * 1024,
                            'PrefixActive'     => false,
                            'PrefixValue'      => '',
                            'SuffixActive'     => true,
                            'SuffixValue'      => ' GB',
                            'DigitsActive'     => true,
                            'DigitsValue'      => 2,
                            'IconActive'       => false,
                            'IconValue'        => '',
                            'ColorActive'      => false,
                            'Color'            => -1
                        ],
                    ]
                ]
            ]
        ];
        protected static $ReadRequest = [[
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetSdCardStatus,
            \TpLink\Api\Protocol::Params=> [
                'harddisk_manage' => [
                    'table' => 'hd_info'
                ]
            ]

        ], [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetCircularRecordingConfig,
            \TpLink\Api\Protocol::Params=> [
                'harddisk_manage' => [
                    'name' => 'harddisk'
                ]
            ]
        ]];
    }
    /*
    class Timezone extends Component
    {
        protected static $ReadRequest = [[
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetClockStatus,
            \TpLink\Api\Protocol::Params=> [
                'system' => [
                    'name' => 'clock_status'
                ]
            ]

        ], [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetDstRule,
            \TpLink\Api\Protocol::Params=> [
                'system' => [
                    'name' => 'dst'
                ]
            ]

        ], [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetTimezone,
            \TpLink\Api\Protocol::Params=> [
                'system' => [
                    'name' => 'dst'
                ]
            ]
        ]];

        [
            {
                "method": "getClockStatus",
                "result": {
                    "system": {
                        "clock_status": {
                            "seconds_from_1970": 1735586164,
                            "local_time": "2024-12-30 20:16:04"
                        }
                    }
                },
                "error_code": 0
            },
            {
                "method": "getDstRule",
                "result": {
                    "system": {
                        "dst": {
                            "enabled": "1",
                            "synced": "0",
                            "has_rule": "0"
                        }
                    }
                },
                "error_code": 0
            },
            {
                "method": "getTimezone",
                "result": {
                    "system": {
                        "dst": {
                            "enabled": "1",
                            "synced": "0",
                            "has_rule": "0"
                        }
                    }
                },
                "error_code": 0
            }
        ]

    }
     */
    class Led extends Component
    {
        public static $Variables = [
            'Led__led__config__enabled' => [
                \TpLink\IPSVarName        => 'LED active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetLedStatus,
            \TpLink\Api\Protocol::Params=> [
                'led' => [
                    'name' => 'config'
                ]
            ]
        ];

        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetLedStatus
        ];
    }
    class Detection extends Component
    {
        public static $Variables = [
            'Detection__motion_detection__motion_det__enabled' => [
                \TpLink\IPSVarName        => 'Motion detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'Detection__motion_detection__motion_det__digital_sensitivity' => [
                \TpLink\IPSVarName        => 'Motion detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'motion_detection' => [
                    'name' => 'motion_det'
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetDetectionConfig
        ];
    }
    /*
    class DetectionRegion extends Component
    {
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetDetectionRegion,
            \TpLink\Api\Protocol::Params=> [
                'motion_detection' => [
                    'table' => [
                        'region_info'
                    ]
                ]
            ]
        ];
        [
            {
                "method": "getDetectionRegion",
                "result": {
                    "motion_detection": {
                        "region_info": [
                            {
                                "region_info_1": {
                                    "x_coor": "0",
                                    "y_coor": "0",
                                    "width": "10000",
                                    "height": "10000"
                                }
                            }
                        ]
                    }
                },
                "error_code": 0
            }
        ]

    }
     */
    class PersonDetection extends Component
    {
        public static $Variables = [
            'PersonDetection__people_detection__detection__enabled' => [
                \TpLink\IPSVarName        => 'Person detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'PersonDetection__people_detection__detection__sensitivity' => [
                \TpLink\IPSVarName        => 'Person detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetPersonDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'people_detection' => [
                    'name' => [
                        'detection'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetPersonDetectionConfig
        ];
    }
    class BabyCryDetection extends Component
    {
        public static $Variables = [
            'BabyCryDetection__sound_detection__bcd__enabled' => [
                \TpLink\IPSVarName        => 'Baby cry detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'BabyCryDetection__sound_detection__bcd__digital_sensitivity' => [
                \TpLink\IPSVarName        => 'Baby cry detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetBCDConfig,
            \TpLink\Api\Protocol::Params=> [
                'sound_detection' => [
                    'name' => [
                        'bcd'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetBCDConfig
        ];
    }
    class VehicleDetection extends Component
    {
        public static $Variables = [
            'VehicleDetection__vehicle_detection__detection__enabled' => [
                \TpLink\IPSVarName        => 'Vehicle detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'VehicleDetection__vehicle_detection__detection__digital_sensitivity' => [
                \TpLink\IPSVarName        => 'Vehicle detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetVehicleDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'vehicle_detection' => [
                    'name' => [
                        'detection'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetVehicleDetectionConfig
        ];
    }
    class PetDetection extends Component
    {
        public static $Variables = [
            'PetDetection__pet_detection__detection__enabled' => [
                \TpLink\IPSVarName        => 'Pet detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'PetDetection__pet_detection__detection__sensitivity' => [
                \TpLink\IPSVarName        => 'Pet detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetPetDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'pet_detection' => [
                    'name' => [
                        'detection'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetPetDetectionConfig
        ];
    }
    class BarkDetection extends Component
    {
        public static $Variables = [
            'BarkDetection__bark_detection__detection__enabled' => [
                \TpLink\IPSVarName        => 'Bark detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'BarkDetection__bark_detection__detection__sensitivity' => [
                \TpLink\IPSVarName        => 'Bark detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetBarkDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'bark_detection' => [
                    'name' => [
                        'detection'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetBarkDetectionConfig
        ];
    }
    class MeowDetection extends Component
    {
        public static $Variables = [
            'MeowDetection__meow_detection__detection__enabled' => [
                \TpLink\IPSVarName        => 'Meow detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'MeowDetection__meow_detection__detection__sensitivity' => [
                \TpLink\IPSVarName        => 'Meow detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetMeowDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'meow_detection' => [
                    'name' => [
                        'detection'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetMeowDetectionConfig
        ];
    }
    class GlassDetection extends Component
    {
        public static $Variables = [
            'GlassDetection__glass_detection__detection__enabled' => [
                \TpLink\IPSVarName        => 'Glass detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'GlassDetection__glass_detection__detection__sensitivity' => [
                \TpLink\IPSVarName        => 'Glass detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetGlassDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'glass_detection' => [
                    'name' => [
                        'detection'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetGlassDetectionConfig
        ];
    }
    class TamperDetection extends Component
    {
        public static $Variables = [
            'TamperDetection__tamper_detection__tamper_det__enabled' => [
                \TpLink\IPSVarName        => 'Tamper detection active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'TamperDetection__tamper_detection__tamper_det__digital_sensitivity' => [
                \TpLink\IPSVarName        => 'Tamper detection sensitivity',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 100,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => true,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' %',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetTamperDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'tamper_detection' => [
                    'name' => 'tamper_det'
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetTamperDetectionConfig
        ];
    }
    /*
    class LinecrossingDetection extends Component
    {
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetLinecrossingDetectionConfig,
            \TpLink\Api\Protocol::Params=> [
                'linecrossing_detection' => [
                    'name' => [
                        'detection',
                        'arming_schedule'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetLinecrossingDetectionConfig
        ];
    }
    class LinecrossingDetectionRegion extends Component
    {
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetLinecrossingDetectionRegion,
            \TpLink\Api\Protocol::Params=> [
                'linecrossing_detection' => [
                    'table' => [
                        'region_info'
                    ]
                ]
            ]
        ];
    }
     */
    class TargetTrack extends Component
    {
        public static $Variables = [
            'TargetTrack__target_track__target_track_info__enabled' => [
                \TpLink\IPSVarName        => 'Target Track active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetTargetTrackConfig,
            \TpLink\Api\Protocol::Params=> [
                'target_track' => [
                    'name' => [
                        'target_track_info'
                    ]
                ]
            ]
        ];

        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetTargetTrackConfig,
        ];

    }
    class Alert extends Component
    {
        public static $Variables = [
            'Alert__msg_alarm__chn1_msg_alarm_info__enabled' => [
                \TpLink\IPSVarName        => 'Alert enabled',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            /* Funktioniert nicht zu schalten
            'Alert__msg_alarm__chn1_msg_alarm_info__light_alarm_enabled' => [
                \TpLink\IPSVarName        => 'Alert light enabled',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'Alert__msg_alarm__chn1_msg_alarm_info__sound_alarm_enabled' => [
                \TpLink\IPSVarName        => 'Alert sound enabled',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
             */
            'Alert__msg_alarm__chn1_msg_alarm_info__alarm_volume' => [
                \TpLink\IPSVarName        => 'Alert volume',
                \TpLink\IPSVarType        => VARIABLETYPE_STRING,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_ENUMERATION,
                    'OPTIONS'            => [
                        [
                            'Value'              => 'low',
                            'Caption'            => 'Low',
                            'IconActive'         => false,
                            'IconValue'          => '',
                            'Color'              => -1
                        ], [
                            'Value'              => 'normal',
                            'Caption'            => 'Medium',
                            'IconActive'         => false,
                            'IconValue'          => '',
                            'Color'              => -1
                        ], [
                            'Value'              => 'high',
                            'Caption'            => 'High',
                            'IconActive'         => false,
                            'IconValue'          => '',
                            'Color'              => -1
                        ]
                    ]
                ],
                \TpLink\HasAction      => true
            ],
            'Alert__msg_alarm__chn1_msg_alarm_info__alarm_duration' => [
                \TpLink\IPSVarName        => 'Alert duration',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 30,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 5,
                    'PERCENTAGE'                  => false,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' seconds',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true
            ]

        ];
        protected static $ReadRequest = [
            [
                \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetAlertConfig,
                \TpLink\Api\Protocol::Params=> [
                    'msg_alarm' => [
                        'name' => [
                            'chn1_msg_alarm_info'
                        ]
                    ]
                ]
            ],
            [
                \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetAlertEventType,
                \TpLink\Api\Protocol::Params=> [
                    'msg_alarm' => [
                        'table' => [
                            'msg_alarm_type'
                        ]
                    ]
                ]
            ]
            /*
             ,
            [
                \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetAlertPlan,
                \TpLink\Api\Protocol::Params=> [
                    'msg_alarm_plan' => [
                        'table' => [
                            'chn1_msg_alarm_plan'
                        ]
                    ]
                ]
            ]
             */
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetAlertConfig
        ];
        /*
        [
            {
                "method": "getAlertConfig",
                "result": {
                    "msg_alarm": {
                        "chn1_msg_alarm_info": {
                            "enabled": "off",
                            "light_alarm_enabled": "on",
                            "alarm_mode": [
                                "sound",
                                "light"
                            ],
                            "alarm_type": "0",
                            "light_type": "1",
                            "sound_alarm_enabled": "on",
                            "alarm_volume": "high",
                            "alarm_duration": "0"
                        }
                    }
                },
                "error_code": 0
            }
        ]
         */
    }
    class MsgPush extends Component
    {
        public static $Variables = [
            'MsgPush__msg_push__chn1_msg_push_info__notification_enabled' => [
                \TpLink\IPSVarName        => 'Push Notification enabled',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ],
            'MsgPush__msg_push__chn1_msg_push_info__rich_notification_enabled' => [
                \TpLink\IPSVarName        => 'Rich Notification enabled',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ]
        ];
        protected static $ReadRequest = [
            [
                \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetMsgPushConfig,
                \TpLink\Api\Protocol::Params=> [
                    'msg_push' => [
                        'name' => 'chn1_msg_push_info'
                    ]
                ]
            ],
            [
                \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetMsgPushEventList,
                \TpLink\Api\Protocol::Params=> [
                    'msg_push' => [
                        'table' => 'msg_push_event'
                    ]
                ]
            ]
            /*
            ,[
                \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetMsgPushPlan,
                \TpLink\Api\Protocol::Params=> [
                    'msg_push_plan' => [
                        'name' => 'chn1_msg_push_plan'
                    ]
                ]
            ]
             */

        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetMsgPushConfig
        ];
    }
    class LensMask extends Component
    {
        public static $Variables = [
            'LensMask__lens_mask__lens_mask_info__enabled' => [
                \TpLink\IPSVarName        => 'Privacy Mode active',
                \TpLink\IPSVarType        => VARIABLETYPE_BOOLEAN,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                \TpLink\HasAction      => true
            ]
        ];
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetLensMaskConfig,
            \TpLink\Api\Protocol::Params=> [
                'lens_mask' => [
                    'name' => 'lens_mask_info'
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetLensMaskConfig
        ];

    }
    class Battery extends Component
    {
        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetBatteryStatus,
            \TpLink\Api\Protocol::Params=> [
                'battery_manage' => [
                    'name' => 'battery'
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetBatteryConfig
        ];
    }
    class Ptz extends Component
    {
        public static $Variables = [
            'Ptz__preset__goto_preset__id' => [
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER
            ]
        ];

        protected static $ReadRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetPresetConfig,
            \TpLink\Api\Protocol::Params=> [
                'preset' => [
                    'name' => [
                        'preset'
                    ]
                ]
            ]
        ];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::MotorMoveToPreset
        ];
    }
    class Siren extends Component
    {
        public static $Variables = [
            'Siren__siren__volume' => [
                \TpLink\IPSVarName        => 'Bell volume',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 10,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => false,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => '',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true
            ],
            'Siren__siren__duration' => [
                \TpLink\IPSVarName        => 'Bell duration',
                \TpLink\IPSVarType        => VARIABLETYPE_INTEGER,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'                      => 0,
                    'CUSTOM_GRADIENT'             => '[]',
                    'ICON'                        => 'gauge',
                    'DECIMAL_SEPARATOR'           => '',
                    'GRADIENT_TYPE'               => 0,
                    'MAX'                         => 300,
                    'INTERVALS'                   => [],
                    'INTERVALS_ACTIVE'            => false,
                    'MIN'                         => 1,
                    'PERCENTAGE'                  => false,
                    'PREFIX'                      => '',
                    'STEP_SIZE'                   => 1,
                    'SUFFIX'                      => ' seconds',
                    'THOUSANDS_SEPARATOR'         => '',
                    'USAGE_TYPE'                  => 5,
                ],
                \TpLink\HasAction      => true,
            ],
            'Siren__siren__siren_type' => [
                \TpLink\IPSVarName        => 'Siren type',
                \TpLink\IPSVarType        => VARIABLETYPE_STRING,
                \TpLink\IPSVarPresentation=> [
                    \TpLink\PRESENTATION => VARIABLE_PRESENTATION_ENUMERATION
                ],
                \TpLink\HasAction      => true

            ]
        ];
        protected static $ReadRequest = [[
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetSirenTypeList,
            \TpLink\Api\Protocol::Params=> [
                'siren' => [
                    'name' => [
                        'siren'
                    ]
                ]
            ]
        ], [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetSirenStatus,
            \TpLink\Api\Protocol::Params=> [
                'siren' => [
                    'name' => [
                        'siren'
                    ]
                ]
            ]
        ], [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::GetSirenConfig,
            \TpLink\Api\Protocol::Params=> [
                'siren' => [
                    'name' => [
                        'siren'
                    ]
                ]
            ]
        ]];
        protected static $WriteRequest = [
            \TpLink\Api\Protocol::Method=> \TpLink\Api\MethodV3::SetSirenConfig,
            \TpLink\Api\Protocol::Params=> [
                'siren' => [
                    'name' => [
                        'siren'
                    ]
                ]
            ]
        ];
    }
}

namespace TpLink
{
    class DeviceType
    {
        public const Plug = 'TAPOPLUG';
        public const Bulb = 'TAPOBULB';
        public const Camera = 'IPCAMERA';
        public const KasaHub = 'KASAHUB';
        public const Hub = 'TAPOHUB';
        public static $DeviceType = [
            self::Plug    => GUID::Plug,
            self::Bulb    => GUID::BulbWithe,
            self::Camera  => GUID::Camera,
            self::KasaHub => GUID::Hub100,
            self::Hub     => GUID::Hub200
        ];
        public static function GetGuidByDeviceType(string $Type): string
        {
            $Type = explode('.', $Type)[1] ?? '';
            if (array_key_exists($Type, self::$DeviceType)) {
                return self::$DeviceType[$Type];
            }
            return '';
        }
    }
    class DeviceModel
    {
        public const PlugP100 = 'P100'; // WLAN-Steckdose
        public const PlugP105 = 'P105'; // WLAN Steckdose Rund
        public const PlugP110 = 'P110'; // WLAN Steckdose mit Messung
        public const PlugP115 = 'P115'; // WLAN-Steckdose Rund mit Verbrauchsanzeige
        public const PlugP300 = 'P300'; // WLAN Power-Strip
        public const Switch110 = 'S110'; // Smart Switch Relay
        public const Switch112 = 'S112'; // Smart Switch 2x Relay
        public const BulbL510 = 'L510'; // E27-Glühbirne, dimmbar
        public const BulbL520 = 'L520'; // E27-Glühbirne, dimmbar
        public const BulbL530 = 'L530'; // E27-Glühbirne, mehrfarbig
        public const BulbL535 = 'L535'; // E27-Glühbirne, mehrfarbig
        public const BulbL610 = 'L610'; // Wi-Fi Strahler GU10, dimmbar
        public const BulbL630 = 'L630'; // Wi-Fi-Strahler GU10, mehrfarbig
        public const StripeL900 = 'L900'; // Wi-Fi Light Strip RGB
        public const StripeL920 = 'L920'; // Wi-Fi Light Strip Multifarben ? Zonen
        public const StripeL930 = 'L930'; // Wi-Fi Light Strip RGBW , Multifarben 50 Zonen
        public const KH100 = 'KH100'; // Hub mit integrierter Sirene
        public const H100 = 'H100'; // Hub mit integrierter Sirene
        public const H200 = 'H200'; // Hub mit LAN

        public static $DeviceModels = [
            self::PlugP100    => GUID::Plug,
            self::PlugP105    => GUID::Plug,
            self::PlugP110    => GUID::PlugEnergy,
            self::PlugP115    => GUID::PlugEnergy,
            self::PlugP300    => GUID::PlugsMulti,
            self::Switch110   => GUID::PlugEnergy,
            self::Switch112   => GUID::PlugsEnergyMulti,
            self::BulbL510    => GUID::BulbWithe,
            self::BulbL520    => GUID::BulbWithe,
            self::BulbL530    => GUID::BulbColor,
            self::BulbL535    => GUID::BulbColor,
            self::BulbL610    => GUID::BulbWithe,
            self::BulbL630    => GUID::BulbColor,
            self::StripeL900  => GUID::BulbColor,
            self::StripeL920  => GUID::StripeColor,
            self::StripeL930  => GUID::StripeColor,
            self::KH100       => GUID::Hub100,
            self::H100        => GUID::Hub100,
            self::H200        => GUID::Hub200
        ];

        /**
         * GetGuidByDeviceModel
         *
         * @param  string $Model
         * @return string
         */
        public static function GetGuidByDeviceModel(string $Model): string
        {
            $Match = [];
            if (preg_match('/^[a-zA-Z]{1,2}\d{2,3}/', $Model, $Match)) {
                if (array_key_exists($Match[0], self::$DeviceModels)) {
                    return self::$DeviceModels[$Match[0]];
                }
            }
            return '';
        }

    }

    class HubChildDevicesModel
    {
        public const KE100 = 'KE100'; // Heizkörperthermostat
        public const T100 = 'T100'; // Bewegungsmelder
        public const T110 = 'T110'; // Intelligenter Kontaktsensor
        public const T300 = 'T300'; // Wasserlecksensor
        public const T310 = 'T310'; // Temperatur- & Feuchtigkeitsmonitor
        public const T315 = 'T315'; // Temperatur- & Feuchtigkeitsmonitor mit Display
        public const S200 = 'S200'; // Remote Button oder Dimmschalter
        public const S210 = 'S210'; // Lichtschalter 1-fach
        public const S220 = 'S220'; // Lichtschalter 2-fach

        public static $DeviceModels = [
            self::KE100 => GUID::HubChild,
            self::T100  => GUID::HubChild,
            self::T110  => GUID::HubChild,
            self::T300  => GUID::HubChild,
            self::T310  => GUID::HubChild,
            self::T315  => GUID::HubChild,
            self::S200  => GUID::HubChild,
            self::S210  => GUID::HubChild,
            self::S220  => GUID::HubChild
        ];

        /**
         * GetGuidByDeviceModel
         *
         * @param  string $Model
         * @return string
         */
        public static function GetGuidByDeviceModel(string $Model): string
        {
            $Match = [];
            if (preg_match('/^[a-zA-Z]{1,2}\d{3}/', $Model, $Match)) {
                if (array_key_exists($Match[0], self::$DeviceModels)) {
                    return self::$DeviceModels[$Match[0]];
                }
            }
            return '';
        }
    }

    class HubChildDevicesCategory
    {
        public const TRV = 'trv';
        public const TempHmdtSensor = 'temp-hmdt-sensor';
        public const ContantSensor = 'contact-sensor';
        private static $Idents = [
            self::TempHmdtSensor => [
                VariableIdent\Online,
                VariableIdent\Battery,
                VariableIdent\Humidity,
                VariableIdent\Rssi,
                VariableIdent\Temp
            ],
            self::TRV => [
                VariableIdent\Online,
                VariableIdent\Battery,
                VariableIdent\Rssi,
                VariableIdent\Temp,
                VariableIdent\Trv
            ],
            self::ContantSensor => [
                VariableIdent\Online,
                VariableIdent\Battery,
                VariableIdent\Rssi,
                VariableIdent\OpenClose

            ]
        ];

        /**
         * GetVariableIdentsByCategory
         *
         * @param  string $Category
         * @return array
         */
        public static function GetVariableIdentsByCategory(string $Category): array
        {
            $AllIdents = [];
            if (array_key_exists($Category, self::$Idents)) {
                foreach (self::$Idents[$Category] as $VariableIdentClassName) {
                    /** @var VariableIdent $VariableIdentClassName */
                    $AllIdents = array_merge($AllIdents, $VariableIdentClassName::$Variables);
                }
            }
            return $AllIdents;
        }
    }

    class GUID
    {
        public const Plug = '{AAD6F48D-C23F-4C59-8049-A9746DEB699B}';
        public const PlugEnergy = '{B18B6CAA-AB46-495D-9A7A-85FA3A83113A}';
        public const PlugsMulti = '{C923F554-4621-446E-B0D2-1422F2EB84B5}';
        public const PlugsEnergyMulti = '{BC84530B-1A6A-4614-8CCB-C60019EEFFF1}';
        public const BulbColor = '{3C59DCC3-4441-4E1C-A59C-9F8D26CE2E82}';
        public const BulbWithe = '{1B9D73D6-853D-4E2E-9755-2273FD7A6123}';
        public const StripeColor = '{DF8D96FD-9BC7-4A98-B9E2-C8B2FF92B892}';
        public const Hub100 = '{1EDD1EB2-6885-4D87-BA00-9328D74A85C4}';
        public const Hub200 = '{132F21C7-1DC0-4DCA-8DB3-6CD41FAB536D}';
        public const Camera = '{16C67F80-D963-451A-BED3-68B2E7B12F6C}';
        public const HubConfigurator = '{CA1E7005-E5D1-455C-95DF-5ECE8DC50654}';
        public const HubSendToChild = '{A982FDFB-9576-4DAE-9341-5ADCA8B05326}';
        public const ChildSendToHub = '{5377F7F9-4F55-486C-AA61-C4203190065F}';
        public const HubChild = '{DBBC5150-EF75-487B-9407-27C11DDEF6B4}';
        public static $GUIDs = [
            self::Plug,
            self::PlugEnergy,
            self::PlugsMulti,
            self::PlugsEnergyMulti,
            self::BulbColor,
            self::BulbWithe,
            self::StripeColor,
            self::HubConfigurator,
            self::Camera
        ];
        public static function GuidIsHub(string $Guid): bool
        {
            return in_array($Guid, [self::Hub100, self::Hub200]);
        }
    }

    class Property
    {
        public const Open = 'Open';
        public const Host = 'Host';
        public const Mac = 'Mac';
        public const Username = 'Username';
        public const Password = 'Password';
        public const Interval = 'Interval';
        public const AutoRename = 'AutoRename';
        public const Protocol = 'Protocol';
        public const EncryptType = 'EncryptType';
        public const DeviceId = 'DeviceId';
        public const LightEffectsEnabled = 'LightEffectsEnabled';
        public const UsernameCamera = 'UsernameCamera';
        public const PasswordCamera = 'PasswordCamera';
        public const Stream = 'Stream';
    }

    class Attribute
    {
        public const Username = 'Username';
        public const Password = 'Password';
        public const Category = 'Category';
        public const LightEffects = 'Multi_LightEffects';
        public const LightEffectsEnabled = 'LightEffects';
    }

    class Timer
    {
        public const RequestState = 'RequestState';
    }

    /*
    class AlarmType
    {
        public const Timing = 1;
        public const Motion = 2;
        public const Tamper = 3;
        public const LineCrossing = 4;
        public const AreaIntrusion = 5;
        public const Person = 6;
        public const BabyCry = 7;
        public const Vehicle = 8;
        public const Pet = 9;
        public const RingAlarm = 10;
        public const DogBark = 11;
        public const CatMeow = 12;
        public const GlassAlarm = 13;
        public const SmokeAlarm = 14;
        public const DeliverPackage = 15;
        public const PickupPackage = 16;
        public const DoorbellRingMissed = 17;
        public const DoorbellRingAnswered = 18;
        public const AntiTheft = 19;

        public static $AlarmTypeNames = [
            self::Timing                => 'Timing',
            self::Motion                => 'Motion',
            self::Tamper                => 'Tamper',
            self::LineCrossing          => 'Line Crossing',
            self::AreaIntrusion         => 'Area Intrusion',
            self::Person                => 'Person',
            self::BabyCry               => 'Baby Cry',
            self::Vehicle               => 'Vehicle',
            self::Pet                   => 'Pet',
            self::RingAlarm             => 'Ring Alarm',
            self::DogBark               => 'Dog Bark',
            self::CatMeow               => 'Cat Meow',
            self::GlassAlarm            => 'Glass Alarm',
            self::SmokeAlarm            => 'Smoke Alarm',
            self::DeliverPackage        => 'Deliver Package',
            self::PickupPackage         => 'Pickup Package',
            self::DoorbellRingMissed    => 'Doorbell Ring Missed',
            self::DoorbellRingAnswered  => 'Doorbell Ring Answered',
            self::AntiTheft             => 'Anti Theft',
        ];

        public static function getAlarmTypeName(int $Type): string
        {
            return self::$AlarmTypeNames[$Type] ?? 'Unknown';
        }
    }
     */

}
