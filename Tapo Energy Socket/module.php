<?php

declare(strict_types=1);
require_once dirname(__DIR__) . '/Tapo Socket/module.php';

/**
 * TapoEnergySocket Klasse für die Anbindung von TP-Link tapo Smart Sockets mit Energiemessung.
 * Erweitert TapoSocket.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.60
 */
class TapoEnergySocket extends TapoSocket
{
    public function ApplyChanges()
    {
        //Never delete this line!
        $this->RegisterProfileInteger(\TpLink\VariableProfile::Runtime, '', '', ' minutes', 0, 0, 0);
        $this->RegisterVariableString(\TpLink\VariableIdentEnergySocket::today_runtime, $this->Translate('Runtime today'));
        $this->RegisterVariableString(\TpLink\VariableIdentEnergySocket::month_runtime, $this->Translate('Runtime month'));
        $this->RegisterVariableInteger(\TpLink\VariableIdentEnergySocket::today_runtime_raw, $this->Translate('Runtime today (minutes)'), \TpLink\VariableProfile::Runtime);
        $this->RegisterVariableInteger(\TpLink\VariableIdentEnergySocket::month_runtime_raw, $this->Translate('Runtime month (minutes)'), \TpLink\VariableProfile::Runtime);
        $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::today_energy, $this->Translate('Energy today'), '~Electricity.Wh');
        $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::month_energy, $this->Translate('Energy month'), '~Electricity.Wh');
        $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::current_power, $this->Translate('Current power'), '~Watt');
        parent::ApplyChanges();
    }

    public function RequestState()
    {
        if (parent::RequestState()) {
            $Result = $this->GetEnergyUsage();
            if (is_array($Result)) {
                $this->SetValue(\TpLink\VariableIdentEnergySocket::today_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::today_runtime]);
                $this->SetValue(\TpLink\VariableIdentEnergySocket::month_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::month_runtime]);
                $this->SetValue(\TpLink\VariableIdentEnergySocket::today_runtime, sprintf(gmdate('H \%\s i \%\s', $Result[\TpLink\VariableIdentEnergySocket::today_runtime] * 60), $this->Translate('hours'), $this->Translate('minutes')));
                $this->SetValue(\TpLink\VariableIdentEnergySocket::month_runtime, sprintf(gmdate('z \%\s H \%\s i \%\s', $Result[\TpLink\VariableIdentEnergySocket::month_runtime] * 60), $this->Translate('days'), $this->Translate('hours'), $this->Translate('minutes')));
                $this->SetValue(\TpLink\VariableIdentEnergySocket::today_energy, $Result[\TpLink\VariableIdentEnergySocket::today_energy]);
                $this->SetValue(\TpLink\VariableIdentEnergySocket::month_energy, $Result[\TpLink\VariableIdentEnergySocket::month_energy]);
                $this->SetValue(\TpLink\VariableIdentEnergySocket::current_power, ($Result[\TpLink\VariableIdentEnergySocket::current_power] / 1000));
                return true;
            }
        }
        return false;
    }

    public function GetEnergyUsage()
    {
        $Request = json_encode([
            'method'         => 'get_energy_usage',
            'requestTimeMils'=> 0
        ]);
        $this->SendDebug(__FUNCTION__, $Request, 0);
        $Response = $this->SendRequest($Request);
        if ($Response === '') {
            return false;
        }
        $json = json_decode($Response, true);
        if ($json['error_code'] != 0) {
            trigger_error(\TpLink\Api\Protocol::$ErrorCodes[$json['error_code']], E_USER_NOTICE);
            return false;
        }
        return $json['result'];
    }
}