<?php

declare(strict_types=1);

namespace TpLink{
    class VariableIdentEnergySocket
    {
        public const today_runtime = 'today_runtime';
        public const month_runtime = 'month_runtime';
        public const today_runtime_raw = 'today_runtime_raw';
        public const month_runtime_raw = 'month_runtime_raw';
        public const today_energy = 'today_energy';
        public const month_energy = 'month_energy';
        public const current_power = 'current_power';
    }
}

namespace {
    require_once dirname(__DIR__) . '/Tapo Socket/module.php';

    /**
     * TapoEnergySocket Klasse für die Anbindung von WiFi Sockets mit Energiemessung.
     * Erweitert TapoSocket.
     *
     * @author        Michael Tröger <micha@nall-chan.net>
     * @copyright     2024 Michael Tröger
     * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
     *
     * @version       1.70
     */
    class TapoEnergySocket extends TapoSocket
    {
        /**
         * ApplyChanges
         *
         * @return void
         */
        public function ApplyChanges(): void
        {
            //Never delete this line!

            $this->UnregisterProfile(\TpLink\VariableProfile::Runtime);
            $this->RegisterVariableInteger(
                \TpLink\VariableIdentEnergySocket::today_runtime_raw,
                $this->Translate('Runtime today'),
                [
                    \TpLink\PRESENTATION    => VARIABLE_PRESENTATION_DURATION,
                    'COUNTDOWN_TYPE'        => 0,
                    'FORMAT'                => 2,
                    'MILLISECONDS'          => false
                ]
            );
            $this->RegisterVariableInteger(\TpLink\VariableIdentEnergySocket::month_runtime_raw, $this->Translate('Runtime month'), [
                \TpLink\PRESENTATION    => VARIABLE_PRESENTATION_DURATION,
                'COUNTDOWN_TYPE'        => 0,
                'FORMAT'                => 2,
                'MILLISECONDS'          => false
            ]);
            $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::today_energy, $this->Translate('Energy today'), '~Electricity.Wh');
            $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::month_energy, $this->Translate('Energy month'), '~Electricity.Wh');
            $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::current_power, $this->Translate('Current power'), '~Watt');
            parent::ApplyChanges();
        }

        /**
         * RequestState
         *
         * @return bool
         */
        public function RequestState(): bool
        {
            if (parent::RequestState()) {
                $Result = $this->GetEnergyUsage();
                if (is_array($Result)) {
                    $this->SetValue(\TpLink\VariableIdentEnergySocket::today_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::today_runtime] * 60);
                    $this->SetValue(\TpLink\VariableIdentEnergySocket::month_runtime_raw, $Result[\TpLink\VariableIdentEnergySocket::month_runtime] * 60);
                    $this->SetValue(\TpLink\VariableIdentEnergySocket::today_energy, $Result[\TpLink\VariableIdentEnergySocket::today_energy]);
                    $this->SetValue(\TpLink\VariableIdentEnergySocket::month_energy, $Result[\TpLink\VariableIdentEnergySocket::month_energy]);
                    $this->SetValue(\TpLink\VariableIdentEnergySocket::current_power, ($Result[\TpLink\VariableIdentEnergySocket::current_power] / 1000));
                    return true;
                }
            }
            return false;
        }

        /**
         * GetEnergyUsage
         *
         * @return false
         */
        public function GetEnergyUsage(): false|array
        {
            $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetEnergyUsage);
            $Response = $this->SendRequest($Request);
            if ($Response === null) {
                return false;
            }
            return $Response;
        }
    }
}