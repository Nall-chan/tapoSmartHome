<?php

declare(strict_types=1);

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
    use \TpLink\EnergyUsage;

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
        $this->RegisterVariableInteger(
            \TpLink\VariableIdentEnergySocket::month_runtime_raw,
            $this->Translate('Runtime month'),
            [
                \TpLink\PRESENTATION    => VARIABLE_PRESENTATION_DURATION,
                'COUNTDOWN_TYPE'        => 0,
                'FORMAT'                => 2,
                'MILLISECONDS'          => false
            ]
        );
        $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::today_energy, $this->Translate('Energy today'), '~Electricity.Wh');
        $this->RegisterVariableFloat(\TpLink\VariableIdentEnergySocket::month_energy, $this->Translate('Energy month'), '~Electricity.Wh');
        parent::ApplyChanges();
    }
}
