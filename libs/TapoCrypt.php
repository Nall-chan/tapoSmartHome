<?php

declare(strict_types=1);

/**
 * TapoCrypt
 * Enthält Klassen und Traits für IPSModule-Klasse zur Kommunikation mit Netzwerk-Geräten.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.70
 */

namespace {
    $AutoLoader = new AutoLoaderTapoPHPSecLib('Crypt/Random');
    $AutoLoader->register();

    /**
     * AutoLoaderTapoPHPSecLib
     */
    class AutoLoaderTapoPHPSecLib
    {
        private $namespace;

        /**
         * __construct
         *
         * @param  mixed $namespace
         * @return void
         */
        public function __construct($namespace = null)
        {
            $this->namespace = $namespace;
        }

        /**
         * register
         *
         * @return void
         */
        public function register()
        {
            spl_autoload_register([$this, 'loadClass']);
        }

        /**
         * loadClass
         *
         * @param  mixed $className
         * @return void
         */
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

namespace TpLink\Crypt
{
    const HTTP = 'http://';
    const HTTPS = 'https://';

    /**
     * Url
     */
    class Url
    {
        public const App = '/app';
        public const InitKlap = self::App . '/handshake1';
        public const HandshakeKlap = self::App . '/handshake2';
        public const KlapRequest = self::App . '/request?';
        public const Token = 'token';
    }

    /**
     * Method
     */
    class Method
    {
        public const SecurePassthrough = 'securePassthrough';
    }

    /**
     * Protocol
     */
    class Protocol
    {
        private const Method = 'method';
        private const Params = 'params';
        private const Request = 'request';

        /**
         * BuildSecurePassthroughRequest
         *
         * @param  string $EncryptedPayload
         * @return string
         */
        public static function BuildSecurePassthroughRequest(string $EncryptedPayload): string
        {
            return json_encode([
                self::Method=> Method::SecurePassthrough,
                self::Params=> [
                    self::Request=> $EncryptedPayload
                ]]);
        }
    }

    class SSLAESCipher
    {
    }

    /**
     * @property string $LocalNonce
     * @property string $ServerNonce
     * @property string $PwdHash
     * @property string $TpLinkCipherIV
     * @property string $TpLinkCipherKey
     * @property string $TokenUrl
     * @property string $Username
     * @property ?int $KlapSequenz
     */
    trait SSLAESSecurePassthrough
    {
        private function InitSSLAES(): bool
        {
            switch ($this->Handshake1SSLAES()) {
                case 0:
                    $this->SendDebug(__FUNCTION__, 'Handshake successfully', 0);
                    break;
                case 1:
                    return true;
                case 2:
                    return false;
            }
            if (!$this->Handshake2SSLAES()) {
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($this->Translate('Handshake failed'), E_USER_NOTICE);
                restore_error_handler();
                return false;
            }

            return true;
        }
        private function Handshake1SSLAES(string $Username = '', string $Password = ''): int
        {
            $Username = $Username ?: $this->ReadPropertyString(\TpLink\Property::Username);
            $Password = $Password ?: $this->ReadPropertyString(\TpLink\Property::Password);
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host);
            $LocalNonce = strtoupper(bin2hex(random_bytes(8)));
            $Payload = json_encode(\TpLink\Api\Protocol::BuildRequest(
                \TpLink\Api\Method::Login,
                '',
                [
                    \TpLink\Api\Param::CNonce     => $LocalNonce,
                    \TpLink\Api\Param::EncryptType=> '3',
                    \TpLink\Api\Param::Username   => $Username
                ]
            ));
            $this->SendDebug(__FUNCTION__, $Payload, 0);
            $Result = $this->CurlRequest($Url, $Payload);
            if ($Result === false) {
                return 2;
            }
            $Response = json_decode($Result, true);
            $this->SendDebug(__FUNCTION__ . ' Result', $Result, 0);
            $this->SendDebug(__FUNCTION__ . ' Result', $Response, 0);
            if (
                $Response != null &&
                self::isLessSecureLogin($Response) &&
                 self::GetResponseInnerError($Response) != \TpLink\Api\ErrorCodes::BadUsername &&
                 $this->TryLessSecureLogin($Username, $Password)) {
                $this->SendDebug(__FUNCTION__, 'LessSecureLoginSuccessfully', 0);
                return 1;
            }
            if (
                $Response != null &&
                isset($Response[\TpLink\Api\ErrorCode]) &&
                $Response[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::InvalidNonce &&
                !isset($Response[\TpLink\Api\Result][\TpLink\Api\Result::Data][\TpLink\Api\Result::Nonce])
            ) {
                if (self::GetResponseInnerError($Response) == \TpLink\Api\ErrorCodes::DeviceBlocked) {
                    $Seconds = $Response[\TpLink\Api\Result::Data][\TpLink\Api\Result::SecLeft];
                    $this->SendDebug(__FUNCTION__, \TpLink\Api\ErrorCodes::getText(\TpLink\Api\ErrorCodes::DeviceBlocked) . $Seconds . ' seconds', 0);
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate(\TpLink\Api\ErrorCodes::getText(\TpLink\Api\ErrorCodes::DeviceBlocked)) . $Seconds . $this->Translate(' seconds'), E_USER_NOTICE);
                    restore_error_handler();
                    return 2;
                }
                if (self::GetResponseInnerError($Response) == \TpLink\Api\ErrorCodes::InvalidUsername) {
                    $this->SendDebug(__FUNCTION__, 'Try admin login', 0);
                    return $this->Handshake1SSLAES('admin', $Password);
                }
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($this->Translate(\TpLink\Api\ErrorCodes::getText($Response[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
                restore_error_handler();
                return 2;
            }

            if (
                $Response != null &&
                isset($Response[\TpLink\Api\Result][\TpLink\Api\Result::Data][\TpLink\Api\Result::Nonce])
            ) {
                $this->SendDebug(__FUNCTION__, 'Check Hashes', 0);
                $server_nonce = $Response[\TpLink\Api\Result][\TpLink\Api\Result::Data][\TpLink\Api\Result::Nonce] ?? '';
                $device_confirm = $Response[\TpLink\Api\Result][\TpLink\Api\Result::Data][\TpLink\Api\Result::DeviceConfirm] ?? '';
                $pwd_hash = strtoupper(hash('sha256', mb_convert_encoding($Password, 'UTF-8')));
                $confirm_hash = strtoupper(hash('sha256', $LocalNonce . $pwd_hash . $server_nonce) . $server_nonce . $LocalNonce);
                if ($device_confirm != '' && hash_equals($confirm_hash, $device_confirm)) {
                    $this->SendDebug('PWD sha256', $pwd_hash, 0);
                    $this->Username = $Username;
                    $this->PwdHash = $pwd_hash;
                    $this->LocalNonce = $LocalNonce;
                    $this->ServerNonce = $server_nonce;
                    return 0;
                }
                $pwd_hash = strtoupper(hash('md5', mb_convert_encoding($Password, 'UTF-8')));
                $confirm_hash = strtoupper(hash('sha256', $LocalNonce . $pwd_hash . $server_nonce) . $server_nonce . $LocalNonce);
                if ($device_confirm != '' && hash_equals($confirm_hash, $device_confirm)) {
                    $this->SendDebug('PWD md5', $pwd_hash, 0);
                    $this->Username = $Username;
                    $this->PwdHash = $pwd_hash;
                    $this->LocalNonce = $LocalNonce;
                    $this->ServerNonce = $server_nonce;
                    return 0;
                }

            }
            set_error_handler([$this, 'ModulErrorHandler']);
            trigger_error($this->Translate(\TpLink\Api\ErrorCodes::getText($Response[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
            restore_error_handler();
            return 2;

        }
        private function Handshake2SSLAES(): bool
        {
            $DigestPassword = strtoupper(hash('sha256', $this->PwdHash . $this->LocalNonce . $this->ServerNonce) . $this->LocalNonce . $this->ServerNonce);
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host);
            $Payload = json_encode(\TpLink\Api\Protocol::BuildRequest(
                \TpLink\Api\Method::Login,
                '',
                [
                    \TpLink\Api\Param::EncryptType       => '3',
                    \TpLink\Api\Param::CNonce            => $this->LocalNonce,
                    \TpLink\Api\Param::DigestPassword    => $DigestPassword,
                    \TpLink\Api\Param::Username          => $this->Username
                ]
            ));
            $this->SendDebug(__FUNCTION__, $Payload, 0);
            $Result = $this->CurlRequest($Url, $Payload);
            if ($Result === false) {
                return false;
            }
            $Response = json_decode($Result, true);
            $this->SendDebug(__FUNCTION__ . ' Result', $Result, 0);
            $this->SendDebug(__FUNCTION__ . ' Result', $Response, 0);
            if (
                $Response == null
            ) {
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($this->Translate(\TpLink\Api\ErrorCodes::getText(\TpLink\Api\ErrorCodes::NotReachable)), E_USER_NOTICE);
                restore_error_handler();
                return false;
            }

            if ($Response[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($Response[\TpLink\Api\ErrorCode] . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($Response[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
                restore_error_handler();
                return false;
            }
            $this->TokenUrl = $Url . '/stok=' . ($Response[\TpLink\Api\Result]['stok']) . '/ds';
            $this->TpLinkCipherKey = self::GenerateEncryptionToken('lsk', $this->LocalNonce, $this->ServerNonce, $this->PwdHash);
            $this->TpLinkCipherIV = self::GenerateEncryptionToken('ivb', $this->LocalNonce, $this->ServerNonce, $this->PwdHash);
            $this->KlapSequenz = $Response[\TpLink\Api\Result]['start_seq'] ?? 0;
            return true;
        }
        private static function GenerateEncryptionToken(string $TokenType, string $LocalNonce, string $ServerNonce, string $PwdHash): string
        {
            $hashedKey = strtoupper(hash('sha256', $LocalNonce . $PwdHash . $ServerNonce));
            return substr(
                hash('sha256', $TokenType . $LocalNonce . $ServerNonce . $hashedKey, true),
                0,
                16
            );
        }
        private static function GenerateTag(string $Request, string $LocalNonce, string $PwdHash, int $Seq): string
        {
            $pwd_nonce_hash = strtoupper(hash('sha256', $PwdHash . $LocalNonce));
            return strtoupper(hash('sha256', $pwd_nonce_hash . $Request . $Seq));
        }
        private function TryLessSecureLogin(string $Username, string $Password): bool
        {
            $pwd_hash = strtoupper(hash('md5', mb_convert_encoding($Password, 'UTF-8')));
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host);
            $Payload = json_encode(\TpLink\Api\Protocol::BuildRequest(
                \TpLink\Api\Method::Login,
                '',
                [
                    \TpLink\Api\Param::Hashed     => true,
                    \TpLink\Api\Param::Username   => $Username,
                    \TpLink\Api\Param::Password   => $pwd_hash
                ]
            ));
            $this->SendDebug(__FUNCTION__ . ' Less Secure Payload', $Payload, 0);
            $Result = $this->CurlRequest($Url, $Payload);
            if ($Result === false) {
                return false;
            }
            $this->SendDebug(__FUNCTION__ . ' Less Secure Result', $Result, 0);
            $Response = json_decode($Result, true);
            $this->SendDebug(__FUNCTION__ . ' Less Secure Result', $Response, 0);
            if ($Response != null && isset($Response[\TpLink\Api\ErrorCode]) && $Response[\TpLink\Api\ErrorCode] == \TpLink\Api\ErrorCodes::Success) {
                $this->TokenUrl = $Url . '/stok=' . $Response[\TpLink\Api\Result]['stok'] . '/ds';
                $this->TpLinkCipherKey = '';
                $this->TpLinkCipherIV = '';
                $this->Username = $Username;
                $this->PwdHash = $pwd_hash;
                return true;
            }
            return false;
        }

        private static function isLessSecureLogin(?array $Response): bool
        {
            return
                isset($Response[\TpLink\Api\ErrorCode]) &&
                $Response[\TpLink\Api\ErrorCode] == \TpLink\Api\ErrorCodes::SessionExpired &&
                isset($Response[\TpLink\Api\Result][\TpLink\Api\Result::Data][\TpLink\Api\Result::EncryptType][0]) &&
                $Response[\TpLink\Api\Result][\TpLink\Api\Result::Data][\TpLink\Api\Result::EncryptType][0] == '3';
        }
        /**
         * AESEncryptedRequest
         *
         * @param  string $Payload
         * @return string
         */
        private function SSLAESEncryptedRequest(string $Payload): string
        {
            $tp_link_cipher = new \TpLink\Crypt\AESCipher($this->TpLinkCipherKey, $this->TpLinkCipherIV);
            $EncryptedPayload = $tp_link_cipher->encrypt($Payload);
            $SecurePassthroughPayload = \TpLink\Crypt\Protocol::BuildSecurePassthroughRequest($EncryptedPayload);
            //$this->SendDebug('Secure Passthrough Payload', $SecurePassthroughPayload, 0);
            $Headers = [
                'Referer: ' . $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host),
                'Seq: ' . $this->KlapSequenz,
                'Tapo_tag: ' . self::GenerateTag($SecurePassthroughPayload, $this->LocalNonce, $this->PwdHash, $this->KlapSequenz)
            ];
            $this->KlapSequenz++;
            $Result = $this->CurlRequest($this->TokenUrl, $SecurePassthroughPayload, false, $Headers);
            if ($Result === false) {
                return '';
            }
            $json = json_decode($Result, true);
            $json[\TpLink\Api\ErrorCode] = $json[\TpLink\Api\ErrorCode] ?? $json[\TpLink\Api\ErrCode];
            $this->SendDebug('Secure Passthrough Response', $Result, 0);
            if (($json[\TpLink\Api\ErrorCode] == \TpLink\Api\ErrorCodes::SessionExpired) ||
            ($json[\TpLink\Api\ErrorCode] == \TpLink\Api\ErrorCodes::SessionTimeout)) {
                // Session invalid, try to reconnect
                $this->SendDebug('Session Timeout', '', 0);
                if (!$this->Init()) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    restore_error_handler();
                    $this->SetStatus(IS_EBASE + 1);
                } else {
                    return $this->SSLAESEncryptedRequest($Payload);
                }
                return '';
            }
            if ($json[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                $this->SendDebug('Response ' . \TpLink\Api\ErrorCode, $json[\TpLink\Api\ErrorCode], 0);
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($json[\TpLink\Api\ErrorCode] . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($json[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
                restore_error_handler();
                return '';
            }
            $decryptedResponse = $tp_link_cipher->decrypt($json[\TpLink\Api\Result][\TpLink\Api\Result::Response]);
            $this->SendDebug('Response', $decryptedResponse, 0);
            return $decryptedResponse;
        }
        private static function GetResponseInnerError(array $Response): ?int
        {
            $ErrorCode = $Response[\TpLink\Api\Result::Data][\TpLink\Api\Result::Code] ?? null;
            if ($ErrorCode == null) {
                $ErrorCode = $Response[\TpLink\Api\Result][\TpLink\Api\Result::Data][\TpLink\Api\Result::Code] ?? null;
            }
            return $ErrorCode;
        }
    }
    /**
     * AESCipher
     */
    class AESCipher
    {
        private $key;
        private $iv;

        /**
         * __construct
         *
         * @param  mixed $key
         * @param  mixed $iv
         * @return void
         */
        public function __construct($key, $iv)
        {
            $this->key = $key;
            $this->iv = $iv;
        }

        /**
         * encrypt
         *
         * @param  string $data
         * @return string
         */
        public function encrypt(string $data): string
        {
            $cipher = new \phpseclib\Crypt\AES(\phpseclib\Crypt\Base::MODE_CBC);
            $cipher->enablePadding();
            $cipher->setIV($this->iv);
            $cipher->setKey($this->key);
            $encrypted = $cipher->encrypt($data);
            return base64_encode($encrypted);
        }

        /**
         * decrypt
         *
         * @param  string $data
         * @return string
         */
        public function decrypt(string $data): string
        {
            $cipher = new \phpseclib\Crypt\AES(\phpseclib\Crypt\Base::MODE_CBC);
            $cipher->enablePadding();
            $cipher->setIV($this->iv);
            $cipher->setKey($this->key);
            $decrypted = $cipher->decrypt(base64_decode($data));
            return $decrypted;
        }
    }

    /**
     *
     * @property string $TokenUrl
     * @property string $cookie
     * @property string $TpLinkCipherIV
     * @property string $TpLinkCipherKey
     */
    trait AESSecurePassthrough
    {
        /**
         * Handshake
         *
         * @return bool|int
         */
        private function HandshakeAES(): bool|int
        {
            $Key = (new \phpseclib\Crypt\RSA())->createKey(1024);
            $privateKey = $Key['privatekey'];
            $publicKey = $Key['publickey'];
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host); // . \TpLink\Crypt\Url::App;
            $Payload = \TpLink\Api\Protocol::BuildHandshakeRequest($publicKey);
            $this->SendDebug('Handshake', $Payload, 0);
            $this->cookie = '';
            $Result = $this->CurlRequest($Url, $Payload, true);
            $this->SendDebug('Handshake Result', $Result, 0);
            if ($Result === false) {
                return false;
            }
            $json = json_decode($Result, true);
            if ($json[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                return $json[\TpLink\Api\ErrorCode];
            }
            $encryptedKey = $json[\TpLink\Api\Result][\TpLink\Api\Result::EncryptedKey];
            $ciphertext = base64_decode($encryptedKey);
            $rsa = new \phpseclib\Crypt\RSA();
            $rsa->loadKey($privateKey);
            $Bytes = $rsa->_rsaes_pkcs1_v1_5_decrypt($ciphertext);
            $Data = str_split($Bytes, 16);
            $this->TpLinkCipherKey = $Data[0];
            $this->TpLinkCipherIV = $Data[1];
            return true;
        }

        /**
         * LoginAES
         *
         * @return bool
         */
        private function LoginAES(): bool
        {
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host) . \TpLink\Crypt\Url::App;
            $Payload = json_encode(\TpLink\Api\Protocol::BuildRequest(
                \TpLink\Api\Method::LoginDevice,
                '',
                [
                    \TpLink\Api\Param::Password => base64_encode($this->ReadPropertyString(\TpLink\Property::Password)),
                    \TpLink\Api\Param::Username => base64_encode(sha1($this->ReadPropertyString(\TpLink\Property::Username)))
                ]
            ));
            $this->SendDebug(__FUNCTION__, $Payload, 0);
            $tp_link_cipher = new \TpLink\Crypt\AESCipher($this->TpLinkCipherKey, $this->TpLinkCipherIV);
            $EncryptedPayload = $tp_link_cipher->encrypt($Payload);
            $SecurePassthroughPayload = \TpLink\Crypt\Protocol::BuildSecurePassthroughRequest($EncryptedPayload);
            $Result = $this->CurlRequest($Url, $SecurePassthroughPayload);
            if ($Result === false) {
                return false;
            }
            $json = json_decode($tp_link_cipher->decrypt(json_decode($Result, true)[\TpLink\Api\Result][\TpLink\Api\Result::Response]), true);
            $this->SendDebug(__FUNCTION__ . ' Result', $json, 0);
            if ($json[\TpLink\Api\ErrorCode] == 0) {
                $this->TokenUrl = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host) . \TpLink\Crypt\Url::App . '?' . http_build_query([\TpLink\Crypt\Url::Token => $json[\TpLink\Api\Result]['token']]);
                return true;
            }
            set_error_handler([$this, 'ModulErrorHandler']);
            trigger_error($json[\TpLink\Api\ErrorCode] . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($json[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
            restore_error_handler();
            return false;
        }

        /**
         * AESEncryptedRequest
         *
         * @param  string $Payload
         * @return string
         */
        private function AESEncryptedRequest(string $Payload): string
        {

            $this->SendDebug(__FUNCTION__, $Payload, 0);
            $tp_link_cipher = new \TpLink\Crypt\AESCipher($this->TpLinkCipherKey, $this->TpLinkCipherIV);
            $EncryptedPayload = $tp_link_cipher->encrypt($Payload);
            $this->SendDebug('Encrypted Payload', $EncryptedPayload, 0);
            $SecurePassthroughPayload = \TpLink\Crypt\Protocol::BuildSecurePassthroughRequest($EncryptedPayload);
            $this->SendDebug('Secure Passthrough Payload', $SecurePassthroughPayload, 0);
            $Result = $this->CurlRequest($this->TokenUrl, $SecurePassthroughPayload);
            if ($Result === false) {
                return '';
            }
            $json = json_decode($Result, true);
            $this->SendDebug('Response', $Result, 0);
            if ($json[\TpLink\Api\ErrorCode] == 9999) {
                // Session Timeout, try to reconnect
                $this->SendDebug('Session Timeout', '', 0);
                if (!$this->Init()) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    restore_error_handler();
                    $this->SetStatus(IS_EBASE + 1);
                } else {
                    return $this->AESEncryptedRequest($Payload);
                }
                return '';
            }
            if ($json[\TpLink\Api\ErrorCode] != \TpLink\Api\ErrorCodes::Success) {
                $this->SendDebug('Response ' . \TpLink\Api\ErrorCode, $json[\TpLink\Api\ErrorCode], 0);
                set_error_handler([$this, 'ModulErrorHandler']);
                trigger_error($json[\TpLink\Api\ErrorCode] . ' ' . $this->Translate(\TpLink\Api\ErrorCodes::getText($json[\TpLink\Api\ErrorCode])), E_USER_NOTICE);
                restore_error_handler();
                return '';
            }
            $decryptedResponse = $tp_link_cipher->decrypt($json[\TpLink\Api\Result][\TpLink\Api\Result::Response]);
            $this->SendDebug('Response', $decryptedResponse, 0);
            return $decryptedResponse;
        }
        /**
         * LessSecureRequest
         *
         * @param  string $Payload
         * @return string
         */
        private function LessSecureRequest(string $Payload): string
        {
            $this->SendDebug(__FUNCTION__, $Payload, 0);
            $Result = $this->CurlRequest($this->TokenUrl, $Payload);
            $this->SendDebug('Response', $Result, 0);
            if ($Result === false) {
                return '';
            }
            $json = json_decode($Result, true);
            if ($json[\TpLink\Api\ErrorCode] == 9999) {
                // Session Timeout, try to reconnect
                $this->SendDebug('Session Timeout', '', 0);
                if (!$this->Init()) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    restore_error_handler();
                    $this->SetStatus(IS_EBASE + 1);
                } else {
                    return $this->LessSecureRequest($Payload);
                }
                return '';
            }
            return $Result;
        }
    }
    /**
     * KlapCipher
     */
    class KlapCipher
    {
        private $key;
        private $seq;
        private $iv;
        private $sig;

        /**
         * __construct
         *
         * @param  string $lSeed
         * @param  string $rSeed
         * @param  string $uHash
         * @param  int|null $Sequenz
         * @return void
         */
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

        /**
         * encrypt
         *
         * @param  string $data
         * @return string
         */
        public function encrypt(string $data): string
        {
            $this->seq++;
            $cipher = new \phpseclib\Crypt\AES(\phpseclib\Crypt\Base::MODE_CBC);
            $cipher->enablePadding();
            $cipher->setIV($this->iv . pack('N', $this->seq));
            $cipher->setKey($this->key);
            $encrypted = $cipher->encrypt($data);
            $signature = hash('sha256', $this->sig . pack('N', $this->seq) . $encrypted, true);
            return $signature . $encrypted;
        }

        /**
         * getSequenz
         *
         * @return int
         */
        public function getSequenz(): int
        {
            return $this->seq;
        }

        /**
         * decrypt
         *
         * @param  string $data
         * @return string
         */
        public function decrypt(string $data): string
        {
            $cipher = new \phpseclib\Crypt\AES(\phpseclib\Crypt\Base::MODE_CBC);
            $cipher->enablePadding();
            $cipher->setIV($this->iv . pack('N', $this->seq));
            $cipher->setKey($this->key);
            $decrypted = $cipher->decrypt(substr($data, 32));
            return $decrypted;
        }
    }

    /**
     * @property string $cookie
     * @property string $KlapLocalSeed
     * @property string $KlapRemoteSeed
     * @property string $KlapUserHash
     * @property ?int $KlapSequenz
     */
    trait Klap
    {
        /**
         * GenerateKlapAuthHash
         *
         * @param  string $Username
         * @param  string $Password
         * @return string
         */
        private function GenerateKlapAuthHash(string $Username, string $Password): string
        {
            return hash('sha256', sha1(mb_convert_encoding($Username, 'UTF-8'), true) .
                    sha1(mb_convert_encoding($Password, 'UTF-8'), true), true);
        }

        /**
         * InitKlap
         *
         * @return bool
         */
        private function InitKlap(): bool
        {
            $UserHash = $this->GenerateKlapAuthHash(
                $this->ReadPropertyString(\TpLink\Property::Username),
                $this->ReadPropertyString(\TpLink\Property::Password)
            );
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host) . \TpLink\Crypt\Url::InitKlap;
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

        /**
         * HandshakeKlap
         *
         * @return bool
         */
        private function HandshakeKlap(): bool
        {
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host) . \TpLink\Crypt\Url::HandshakeKlap;
            $Payload = hash('sha256', $this->KlapRemoteSeed . $this->KlapLocalSeed . $this->KlapUserHash, true);
            $Result = $this->CurlRequest($Url, $Payload, true);
            $this->SendDebug('Klap Handshake Result', $Result, 0);
            return $Result !== false;
        }

        /**
         * KlapEncryptedRequest
         *
         * @param  string $Payload
         * @return string
         */
        private function KlapEncryptedRequest(string $Payload): string
        {
            $this->SendDebug(__FUNCTION__, $Payload, 0);
            if ($this->KlapLocalSeed === '') {
                if (!$this->Init()) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    restore_error_handler();
                    $this->SetStatus(IS_EBASE + 1);
                    return '';
                }
            }
            $TpKlapCipher = new \TpLink\Crypt\KlapCipher($this->KlapLocalSeed, $this->KlapRemoteSeed, $this->KlapUserHash, $this->KlapSequenz);
            $EncryptedPayload = $TpKlapCipher->encrypt($Payload);
            $this->KlapSequenz = $TpKlapCipher->getSequenz();
            $Url = $this->ReadPropertyString(\TpLink\Property::Protocol) . $this->ReadPropertyString(\TpLink\Property::Host) . \TpLink\Crypt\Url::KlapRequest . http_build_query(['seq'=>$this->KlapSequenz]);
            $Result = $this->CurlRequest($Url, $EncryptedPayload);
            if ($Result === false) {
                if (!$this->Init()) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    restore_error_handler();
                    $this->SetStatus(IS_EBASE + 1);
                } else {
                    return $this->KlapEncryptedRequest($Payload);
                }
                return '';
            }
            $decryptedResponse = $TpKlapCipher->decrypt($Result);
            $this->SendDebug('Response', $decryptedResponse, 0);
            $json = json_decode($decryptedResponse, true);
            if ($json[\TpLink\Api\ErrorCode] == 9999) {
                // Session Timeout, try to reconnect
                $this->SendDebug('Session Timeout', '', 0);
                if (!$this->Init()) {
                    set_error_handler([$this, 'ModulErrorHandler']);
                    trigger_error($this->Translate('Not connected'), E_USER_NOTICE);
                    restore_error_handler();
                    $this->SetStatus(IS_EBASE + 1);
                } else {
                    return $this->KlapEncryptedRequest($Payload);
                }
                return '';
            }
            return $decryptedResponse;
        }
    }
}

