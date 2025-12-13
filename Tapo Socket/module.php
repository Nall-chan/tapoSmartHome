<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/libs/TapoDevice.php';

/**
 * TapoSocket Klasse für die Anbindung von WiFi Sockets.
 * Erweitert \TpLink\Device.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.70
 *
 * @method int FindIDForIdent(string $Ident)
 */
class TapoSocket extends \TpLink\Device
{
    protected static $ModuleIdents = [
        \TpLink\VariableIdent\OnOff,
        \TpLink\VariableIdent\Overheated,
        \TpLink\VariableIdent\Rssi,
        \TpLink\VariableIdent\Socket
    ];

    /**
     * Migrate
     *
     * @param  string $JSONData
     * @return string
     */
    public function Migrate(string $JSONData): string
    {
        // Migrate Ident 'State' to 'device_on'
        $Var = $this->FindIDForIdent('State');
        if (IPS_VariableExists($Var)) {
            IPS_SetIdent($Var, \TpLink\VariableIdentOnOff::device_on);
        }
        return $JSONData;
    }

    /**
     * SwitchMode
     *
     * @param  bool $State
     * @return bool
     */
    public function SwitchMode(bool $State): bool
    {
        if ($this->SetDeviceInfo([\TpLink\VariableIdentOnOff::device_on => $State])) {
            $this->SetValue(\TpLink\VariableIdentOnOff::device_on, $State);
            return true;
        }
        return false;
    }

    /**
     * SwitchModeEx
     *
     * @param  bool $State
     * @param  int $Delay
     * @return bool
     */
    public function SwitchModeEx(bool $State, int $Delay): bool
    {
        $Params = [
            'delay'         => $Delay,
            'desired_states'=> [
                'on' => $State
            ],
            'enable'   => true
        ];
        $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::CountdownRule, $this->terminalUUID, $Params);

        $Response = $this->SendRequest($Request);
        if ($Response === null) {
            return false;
        }
        return true;
    }
}
