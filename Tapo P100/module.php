<?php

declare(strict_types=1);

namespace {
    eval('declare(strict_types=1);namespace TapoP100 {?>' . file_get_contents(__DIR__ . '/../libs/helper/BufferHelper.php') . '}');
    eval('declare(strict_types=1);namespace TapoP100 {?>' . file_get_contents(__DIR__ . '/../libs/helper/DebugHelper.php') . '}');
    eval('declare(strict_types=1);namespace TapoP100 {?>' . file_get_contents(__DIR__ . '/../libs/helper/SemaphoreHelper.php') . '}');
    eval('declare(strict_types=1);namespace TapoP100 {?>' . file_get_contents(__DIR__ . '/../libs/helper/VariableProfileHelper.php') . '}');

    $AutoLoader = new AutoLoaderTapoP100PHPSecLib('Crypt/Random');
    $AutoLoader->register();

    /**
     * TapoP100 Klasse für die Anbindung von TP-Link tapo P100 / P110 Smart Sockets.
     * Erweitert IPSModule.
     *
     * @author        Michael Tröger <micha@nall-chan.net>
     * @copyright     2023 Michael Tröger
     * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
     *
     * @version       1.40
     *
     * @example <b>Ohne</b>
     *
     * @property string $terminalUUID
     * @property string $token
     * @property string $cookie
     * @property string $TpLinkCipherIV
     * @property string $TpLinkCipherKey
     * @property string $KlapLocalSeed
     * @property string $KlapRemoteSeed
     * @property string $KlapUserHash
     * @property string $KlapSequenz
     *
     * @method void RegisterProfileInteger(string $Name, string $Icon, string $Prefix, string $Suffix, int $MinValue, int $MaxValue, float $StepSize)
     * @method bool SendDebug(string $Message, mixed $Data, int $Format)
     */
    class TapoP100 extends IPSModule
    {
        use \TapoP100\BufferHelper;
        use \TapoP100\DebugHelper;
        use \TapoP100\Semaphore;
        use \TapoP100\VariableProfileHelper;

        protected static $ErrorCodes = [
            0    => 'Success',
            -1010=> 'Invalid Public Key Length',
            -1501=> 'Invalid Request or Credentials',
            1002 => 'Incorrect Request',
            1003 => 'Invalid Protocol',
            -1003=> 'JSON formatting error ',
            9999 => 'Session Timeout'
        ];

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->RegisterPropertyBoolean('Open', false);
            $this->RegisterPropertyString('Host', '');
            $this->RegisterPropertyString('Username', '');
            $this->RegisterPropertyString('Password', '');
            $this->RegisterPropertyInteger('Interval', 5);
            $this->RegisterPropertyBoolean('AutoRename', false);
            $this->RegisterTimer('RequestState', 0, 'TAPOSH_RequestState($_IPS[\'TARGET\']);');
            $this->terminalUUID = self::guidv4(\phpseclib\Crypt\Random::string(16));
            $this->InitBuffers();
        }

        public function Destroy()
        {
            //Never delete this line!
            parent::Destroy();
        }

        public function ApplyChanges()
        {
            $this->SetTimerInterval('RequestState', 0);
            //Never delete this line!
            parent::ApplyChanges();
            $this->RegisterVariableBoolean('State', $this->Translate('State'), '~Switch');
            $this->EnableAction('State');
            $this->SetSummary($this->ReadPropertyString('Host'));
            $this->InitBuffers();
            if ($this->ReadPropertyBoolean('Open')) {
                if ($this->ReadPropertyString('Host') != '') {
                    if (!$this->Init()) {
                        $this->SetStatus(IS_EBASE + 1);
                    }
                    $this->SetTimerInterval('RequestState', $this->ReadPropertyInteger('Interval') * 1000);
                    return;
                }
            } else {
                $this->SetStatus(IS_INACTIVE);
            }
        }

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'State':
                    return $this->SwitchMode((bool) $Value);
            }
        }

        public function RequestState()
        {
            $Result = $this->GetDeviceInfo();
            if (is_array($Result)) {
                $this->SetValue('State', $Result['device_on']);
                return true;
            }
            return false;
        }

        public function GetDeviceInfo()
        {
            $Request = json_encode([
                'method'         => 'get_device_info',
                'requestTimeMils'=> 0
            ]);
            $this->SendDebug(__FUNCTION__, $Request, 0);
            $Response = $this->SendRequest($Request);
            $this->SendDebug(__FUNCTION__ . ' Result', $Response, 0);
            if ($Response === '') {
                return false;
            }
            $json = json_decode($Response, true);
            if ($json['error_code'] != 0) {
                trigger_error(self::$ErrorCodes[$json['error_code']], E_USER_NOTICE);
                return false;
            }
            $Name = base64_decode($json['result']['nickname']);
            if ($this->ReadPropertyBoolean('AutoRename') && (IPS_GetName($this->InstanceID) != $Name) && ($Name != '')) {
                IPS_SetName($this->InstanceID, $Name);
            }
            return $json['result'];
        }

        public function SwitchMode(bool $State): bool
        {
            $Request = json_encode([
                'method'=> 'set_device_info',
                'params'=> [
                    'device_on'=> $State
                ],
                'requestTimeMils'=> 0,
                'terminalUUID'   => $this->terminalUUID
            ]);
            $this->SendDebug(__FUNCTION__, $Request, 0);
            $Response = $this->SendRequest($Request);
            $this->SendDebug(__FUNCTION__ . ' Result', $Response, 0);
            if ($Response === '') {
                return false;
            }
            $json = json_decode($Response, true);
            if ($json['error_code'] != 0) {
                trigger_error(self::$ErrorCodes[$json['error_code']], E_USER_NOTICE);
                return false;
            }
            $this->SetValue('State', $State);
            return true;
        }

        public function SwitchModeEx(bool $State, int $Delay): bool
        {
            $Request = json_encode([
                'method'=> 'add_countdown_rule',
                'params'=> [
                    'delay'         => $Delay,
                    'desired_states'=> [
                        'on' => $State
                    ],
                    'enable'   => true,
                    'remain'   => $Delay
                ],
                'terminalUUID'   => $this->terminalUUID
            ]);
            $this->SendDebug(__FUNCTION__, $Request, 0);
            $Response = $this->SendRequest($Request);
            $this->SendDebug(__FUNCTION__ . ' Result', $Response, 0);
            if ($Response === '') {
                return false;
            }
            $json = json_decode($Response, true);
            if ($json['error_code'] != 0) {
                trigger_error(self::$ErrorCodes[$json['error_code']], E_USER_NOTICE);
                return false;
            }
            $this->SetValue('State', $State);
            return true;
        }

        protected function SendRequest(string $Request): string
        {
            if ($this->GetStatus() != IS_ACTIVE) {
                if ($this->ReadPropertyBoolean('Open')) {
                    if (!$this->Init()) {
                        trigger_error($this->Translate('Error on reconnect'), E_USER_NOTICE);
                        $this->SetStatus(IS_EBASE + 1);
                        return '';
                    }
                } else {
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    return '';
                }
            }
            if ($this->KlapRemoteSeed !== '') {
                return $this->KlapEncryptedRequest($Request);
            }
            if ($this->token !== '') {
                return $this->EncryptedRequest($Request);
            }
        }

        protected function SetStatus($Status)
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
        }

        private function GenerateKlapAuthHash(string $Username, string $Password): string
        {
            return hash('sha256', sha1(mb_convert_encoding($Username, 'UTF-8'), true) .
                    sha1(mb_convert_encoding($Password, 'UTF-8'), true), true);
        }

        private function InitKlap(): bool
        {
            $UserHash = $this->GenerateKlapAuthHash(
                $this->ReadPropertyString('Username'),
                $this->ReadPropertyString('Password')
            );
            $Url = 'http://' . $this->ReadPropertyString('Host') . '/app/handshake1';
            $Payload = random_bytes(16);
            $this->SendDebug('Init Klap', $Payload, 0);
            $this->cookie = '';
            $Result = $this->CurlRequest($Url, $Payload, true);
            $this->SendDebug('Init Klap Result', $Result, 0);
            if ($Result === false) {
                return false;
            }
            $RemoteSeed = substr($Result, 0, 16);
            $ServerHash = substr($Result, 16);
            $UserTest = hash('sha256', $Payload . $RemoteSeed . $UserHash, true);
            $this->SendDebug('Config User', $UserTest, 0);
            if ($ServerHash == $UserTest) {
                $this->KlapLocalSeed = $Payload;
                $this->KlapRemoteSeed = $RemoteSeed;
                $this->KlapUserHash = $UserHash;
                return true;
            }
            $UserHash = $this->GenerateKlapAuthHash(
                'kasa@tp-link.net',
                'kasaSetup'
            );
            $UserTest = hash('sha256', $Payload . $RemoteSeed . $UserHash, true);
            $this->SendDebug('Generic User', $UserTest, 0);
            if ($ServerHash == $UserTest) {
                $this->KlapLocalSeed = $Payload;
                $this->KlapRemoteSeed = $RemoteSeed;
                $this->KlapUserHash = $UserHash;
                return true;
            }
            $UserHash = $this->GenerateKlapAuthHash(
                '',
                ''
            );
            $UserTest = hash('sha256', $Payload . $RemoteSeed . $UserHash, true);
            $this->SendDebug('Empty User', $UserTest, 0);
            if ($ServerHash == $UserTest) {
                $this->KlapLocalSeed = $Payload;
                $this->KlapRemoteSeed = $RemoteSeed;
                $this->KlapUserHash = $UserHash;
                return true;
            }
            return false;
        }

        private function HandshakeKlap(): bool
        {
            $Url = 'http://' . $this->ReadPropertyString('Host') . '/app/handshake2';
            $Payload = hash('sha256', $this->KlapRemoteSeed . $this->KlapLocalSeed . $this->KlapUserHash, true);
            $this->SendDebug('Handshake Klap', $Payload, 0);
            $Result = $this->CurlRequest($Url, $Payload, true);
            $this->SendDebug('Klap Handshake Result', $Result, 0);
            return  $Result !== false;
        }

        private function KlapEncryptedRequest(string $Payload): string
        {
            if ($this->KlapLocalSeed === '') {
                if (!$this->Init()) {
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    $this->SetStatus(IS_EBASE + 1);
                    return '';
                }
            }
            $TpKlapCipher = new \TapoP100\KlapCipher($this->KlapLocalSeed, $this->KlapRemoteSeed, $this->KlapUserHash, $this->KlapSequenz);
            $EncryptedPayload = $TpKlapCipher->encrypt($Payload);
            $this->KlapSequenz = $TpKlapCipher->getSequenz();
            $Url = 'http://' . $this->ReadPropertyString('Host') . '/app/request?' . http_build_query(['seq'=>$this->KlapSequenz]);
            $this->SendDebug(__FUNCTION__ . '(' . $this->KlapSequenz . ')', $EncryptedPayload, 0);
            $Result = $this->CurlRequest($Url, $EncryptedPayload);
            if ($Result === false) {
                if (!$this->Init()) {
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    $this->SetStatus(IS_EBASE + 1);
                    return '';
                } else {
                    return $this->KlapEncryptedRequest($Payload);
                }
            }
            $this->SendDebug('Response', $Result, 0);
            $decryptedResponse = $TpKlapCipher->decrypt($Result);
            $json = json_decode($decryptedResponse, true);
            if ($json['error_code'] == 9999) {
                // Session Timeout, try to reconnect
                $this->SendDebug('Session Timeout', '', 0);
                if (!$this->Init()) {
                    $this->SetStatus(IS_EBASE + 1);
                }
                return '';
            }
            if ($json['error_code'] != 0) {
                if (array_key_exists($json['error_code'], self::$ErrorCodes)) {
                    $msg = self::$ErrorCodes[$json['error_code']];
                } else {
                    $msg = $decryptedResponse;
                }
                trigger_error($msg, E_USER_NOTICE);
                return '';
            }
            return $decryptedResponse;
        }

        private function Init(): bool
        {
            $Result = $this->Handshake();
            if ($Result === true) {
                if ($this->Login()) {
                    $this->SetStatus(IS_ACTIVE);
                    return true;
                }
                return false;
            }
            if ($Result === false) {
                return false;
            }
            if ($Result === 1003) {
                if ($this->InitKlap()) {
                    if ($this->HandshakeKlap()) {
                        $this->SetStatus(IS_ACTIVE);
                        return true;
                    }
                }
                return false;
            }
            trigger_error(self::$ErrorCodes[$Result], E_USER_NOTICE);
            return false;
        }

        private function Handshake()
        {
            $Key = (new \phpseclib\Crypt\RSA())->createKey(1024);
            $privateKey = $Key['privatekey'];
            $publicKey = $Key['publickey'];
            $Url = 'http://' . $this->ReadPropertyString('Host') . '/app';
            $Payload = json_encode([
                'method'=> 'handshake',
                'params'=> [
                    'key'            => utf8_decode($publicKey),
                    'requestTimeMils'=> 0
                ]

            ]);
            $this->SendDebug('Handshake', $Payload, 0);
            $this->cookie = '';
            $Result = $this->CurlRequest($Url, $Payload, true);
            $this->SendDebug('Handshake Result', $Result, 0);
            if ($Result === false) {
                return false;
            }
            $json = json_decode($Result, true);
            if ($json['error_code'] != 0) {
                return $json['error_code'];
            }
            $encryptedKey = $json['result']['key'];
            $ciphertext = base64_decode($encryptedKey);
            $rsa = new \phpseclib\Crypt\RSA();
            $rsa->loadKey($privateKey);
            $Bytes = $rsa->_rsaes_pkcs1_v1_5_decrypt($ciphertext);
            $Data = str_split($Bytes, 16);
            $this->TpLinkCipherKey = $Data[0];
            $this->TpLinkCipherIV = $Data[1];
            return true;
        }

        private function Login(): bool
        {
            $Url = 'http://' . $this->ReadPropertyString('Host') . '/app';
            $Payload = json_encode([
                'method'=> 'login_device',
                'params'=> [
                    'password'=> base64_encode($this->ReadPropertyString('Password')),
                    'username'=> base64_encode(sha1($this->ReadPropertyString('Username')))
                ],
                'requestTimeMils'=> 0
            ]);
            $this->SendDebug(__FUNCTION__, $Payload, 0);
            $tp_link_cipher = new \TapoP100\TpLinkCipher($this->TpLinkCipherKey, $this->TpLinkCipherIV);
            $EncryptedPayload = $tp_link_cipher->encrypt($Payload);
            $SecurePassthroughPayload = json_encode([
                'method'=> 'securePassthrough',
                'params'=> [
                    'request'=> $EncryptedPayload
                ]]);
            $Result = $this->CurlRequest($Url, $SecurePassthroughPayload);
            if ($Result === false) {
                return false;
            }
            $json = json_decode($tp_link_cipher->decrypt(json_decode($Result, true)['result']['response']), true);
            $this->SendDebug(__FUNCTION__ . ' Result', $json, 0);
            if ($json['error_code'] == 0) {
                $this->token = $json['result']['token'];
                return true;
            }
            trigger_error(self::$ErrorCodes[$json['error_code']], E_USER_NOTICE);
            return false;
        }

        private function EncryptedRequest(string $Payload): string
        {
            $Url = 'http://' . $this->ReadPropertyString('Host') . '/app?token=' . $this->token;
            $tp_link_cipher = new \TapoP100\TpLinkCipher($this->TpLinkCipherKey, $this->TpLinkCipherIV);
            $EncryptedPayload = $tp_link_cipher->encrypt($Payload);
            $SecurePassthroughPayload = json_encode([
                'method'=> 'securePassthrough',
                'params'=> [
                    'request'=> $EncryptedPayload
                ]]);
            $Result = $this->CurlRequest($Url, $SecurePassthroughPayload);
            if ($Result === false) {
                return '';
            }
            $this->SendDebug('Response', $Result, 0);
            $json = json_decode($Result, true);

            if (in_array($json['error_code'], [9999, 1003])) {
                // Session Timeout, try to reconnect
                $this->SendDebug('Session Timeout', '', 0);
                if (!$this->Init()) {
                    $this->SetStatus(IS_EBASE + 1);
                } else {
                    return $this->EncryptedRequest($Payload);
                }
                return '';
            }
            if ($json['error_code'] != 0) {
                if (array_key_exists($json['error_code'], self::$ErrorCodes)) {
                    $msg = self::$ErrorCodes[$json['error_code']];
                } else {
                    $msg = $Result;
                }
                trigger_error($msg, E_USER_NOTICE);
                return '';
            }
            return $tp_link_cipher->decrypt($json['result']['response']);
        }

        private function CurlRequest(string $Url, string $Payload, bool $noError = false)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $Url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $Payload);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
            curl_setopt($ch, CURLOPT_COOKIELIST, $this->cookie);
            $Result = curl_exec($ch);
            $HttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $Cookie = curl_getinfo($ch, CURLINFO_COOKIELIST);
            curl_close($ch);
            $this->CurlDebug($HttpCode);
            if ($HttpCode == 200) {
                $this->cookie = (is_array($Cookie)) ? array_shift($Cookie) : '';
                return $Result;
            }
            if (($HttpCode == 0) && (!$noError)) {
                $this->SetStatus(IS_EBASE + 1);
            }
            return false;
        }

        private function CurlDebug(int $HttpCode): void
        {
            switch ($HttpCode) {
                case 0:
                    $this->SendDebug('Not connected', '', 0);
                    break;
                case  400:
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

        private function InitBuffers()
        {
            $this->token = '';
            $this->cookie = '';
            $this->TpLinkCipherKey = '';
            $this->TpLinkCipherIV = '';
            $this->KlapLocalSeed = '';
            $this->KlapRemoteSeed = '';
            $this->KlapUserHash = '';
            $this->KlapSequenz = null;
        }

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

    class AutoLoaderTapoP100PHPSecLib
    {
        private $namespace;

        public function __construct($namespace = null)
        {
            $this->namespace = $namespace;
        }

        public function register()
        {
            spl_autoload_register([$this, 'loadClass']);
        }

        public function loadClass($className)
        {
            $LibPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'phpseclib' . DIRECTORY_SEPARATOR;
            $file = $LibPath . str_replace(['\\', 'phpseclib3'], [DIRECTORY_SEPARATOR, 'phpseclib'], $className) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}

namespace TapoP100 {
    class TpLinkCipher
    {
        private $key;
        private $iv;

        public function __construct($key, $iv)
        {
            $this->key = $key;
            $this->iv = $iv;
        }
        public function encrypt($data)
        {
            $cipher = new \phpseclib\Crypt\AES('cbc');
            $cipher->enablePadding();
            $cipher->setIV($this->iv);
            $cipher->setKey($this->key);
            $encrypted = $cipher->encrypt($data);
            return base64_encode($encrypted);
        }

        public function decrypt($data)
        {
            $cipher = new \phpseclib\Crypt\AES('cbc');
            $cipher->enablePadding();
            $cipher->setIV($this->iv);
            $cipher->setKey($this->key);
            $decrypted = $cipher->decrypt(base64_decode($data));
            return $decrypted;
        }
    }
}

namespace TapoP100 {
    class KlapCipher
    {
        private $key;
        private $seq;
        private $iv;
        private $sig;

        public function __construct(string $lSeed, string $rSeed, string $uHash, ?int $Sequenz)
        {
            $this->key = substr(hash('sha256', 'lsk' . $lSeed . $rSeed . $uHash, true), 0, 16);
            $this->sig = substr(hash('sha256', 'ldk' . $lSeed . $rSeed . $uHash, true), 0, 28);
            $iv = hash('sha256', 'iv' . $lSeed . $rSeed . $uHash, true);
            if (is_null($Sequenz)) {
                $this->seq = unpack('N', substr($iv, -4))[1];
            } else {
                $this->seq = $Sequenz;
            }
            $this->iv = substr($iv, 0, 12);
        }

        public function encrypt(string $data): string
        {
            $this->seq++;
            $cipher = new \phpseclib\Crypt\AES('cbc');
            $cipher->enablePadding();
            $cipher->setIV($this->iv . pack('N', $this->seq));
            $cipher->setKey($this->key);
            $encrypted = $cipher->encrypt($data);
            $signature = hash('sha256', $this->sig . pack('N', $this->seq) . $encrypted, true);
            return $signature . $encrypted;
        }

        public function getSequenz(): int
        {
            return $this->seq;
        }

        public function decrypt(string $data): string
        {
            $cipher = new \phpseclib\Crypt\AES('cbc');
            $cipher->enablePadding();
            $cipher->setIV($this->iv . pack('N', $this->seq));
            $cipher->setKey($this->key);
            $decrypted = $cipher->decrypt(substr($data, 32));
            return $decrypted;
        }
    }
}
