<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/libs/TapoDevice.php';

/**
 * TapoMultiEnergySockets Klasse für die Anbindung von WiFi Mehrfachsockets.
 * Erweitert \TpLink\Device.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.70
 */
class TapoMultiEnergySockets extends \TpLink\Device
{
    use \TpLink\EnergyUsageChilds;

    protected static $ModuleIdents = [
        \TpLink\VariableIdent\OnOff,
        \TpLink\VariableIdent\Motor,
        \TpLink\VariableIdent\OverheatedStatus,
        \TpLink\VariableIdent\Rssi,
        \TpLink\VariableIdent\Socket
    ];

    /**
     * ApplyChanges
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        $this->ChildIDs = [];
        //Never delete this line!
        parent::ApplyChanges();

        if ($this->GetStatus() == IS_ACTIVE) {
            $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::GetChildDeviceList);
            $Response = $this->SendRequest($Request);
            if ($Response !== null) {
                foreach ($Response[\TpLink\Api\Result::ChildList] as $ChildDevice) {
                    if (isset($ChildDevice[\TpLink\Api\Result::DeviceMode])) {
                        if (($ChildDevice[\TpLink\Api\Result::DeviceMode] == \TpLink\Api\DeviceMode::Switch) && (!isset($ChildDevice[\TpLink\VariableIdentOnOff::device_on]))) {
                            continue;
                        }
                        if (($ChildDevice[\TpLink\Api\Result::DeviceMode] == \TpLink\Api\DeviceMode::Motor) && (!isset($ChildDevice[\TpLink\VariableIdentMotor::motor_status]))) {
                            continue;
                        }
                    }
                    $ChildIDs[$ChildDevice[\TpLink\Api\Result::Position]] = $ChildDevice[\TpLink\Api\Result::DeviceID];
                    $IdentPrefix = 'Pos_' . $ChildDevice[\TpLink\Api\Result::Position] . '_';
                    if (array_key_exists(\TpLink\Api\Result::Nickname, $ChildDevice)) {
                        $NamePrefix = base64_decode($ChildDevice[\TpLink\Api\Result::Nickname]) . ' - ';
                    }
                    $this->RegisterVariableInteger(
                        $IdentPrefix . \TpLink\VariableIdentEnergySocket::today_runtime_raw,
                        $NamePrefix . $this->Translate('Runtime today'),
                        [
                            \TpLink\PRESENTATION    => VARIABLE_PRESENTATION_DURATION,
                            'COUNTDOWN_TYPE'        => 0,
                            'FORMAT'                => 2,
                            'MILLISECONDS'          => false
                        ]
                    );
                    $this->RegisterVariableInteger(
                        $IdentPrefix . \TpLink\VariableIdentEnergySocket::month_runtime_raw,
                        $NamePrefix . $this->Translate('Runtime month'),
                        [
                            \TpLink\PRESENTATION    => VARIABLE_PRESENTATION_DURATION,
                            'COUNTDOWN_TYPE'        => 0,
                            'FORMAT'                => 2,
                            'MILLISECONDS'          => false
                        ]
                    );
                    $this->RegisterVariableFloat($IdentPrefix . \TpLink\VariableIdentEnergySocket::today_energy, $NamePrefix . $this->Translate('Energy today'), '~Electricity.Wh');
                    $this->RegisterVariableFloat($IdentPrefix . \TpLink\VariableIdentEnergySocket::month_energy, $NamePrefix . $this->Translate('Energy month'), '~Electricity.Wh');
                }
                $this->ChildIDs = $ChildIDs;
            }
        }
    }

    /**
     * GetConfigurationForm
     *
     * @return string
     */
    public function GetConfigurationForm(): string
    {
        $Form = json_decode(parent::GetConfigurationForm(), true);
        unset($Form['elements'][3]);
        return json_encode($Form);
    }

    /**
     * SwitchModeSlot
     *
     * @param  int $Index
     * @param  bool $State
     * @return bool
     */
    public function SwitchModeSlot(int $Index, bool $State): bool
    {
        $Ident = 'Pos_' . $Index . '_' . \TpLink\VariableIdentOnOff::device_on;
        if (!array_key_exists($Index, $this->ChildIDs) || !$this->FindIDForIdent($Ident)) {
            set_error_handler([$this, 'ModulErrorHandler']);
            trigger_error($this->Translate('Invalid index'), E_USER_NOTICE);
            restore_error_handler();
            return false;
        }
        $Values[\TpLink\Api\Result::DeviceID] = $this->ChildIDs[$Index];
        $Values[\TpLink\VariableIdentOnOff::device_on] = $State;
        if ($this->SetDeviceInfo($Values)) {
            $this->SetValue($Ident, $State);
            return true;
        }
        return false;
    }

    /**
     * SwitchModeSlotEx
     *
     * @param  int $Index
     * @param  bool $State
     * @param  int $Delay
     * @return bool
     */
    public function SwitchModeSlotEx(int $Index, bool $State, int $Delay): bool
    {
        $Ident = 'Pos_' . $Index . '_' . \TpLink\VariableIdentOnOff::device_on;
        if (!array_key_exists($Index, $this->ChildIDs) || !$this->FindIDForIdent($Ident)) {
            set_error_handler([$this, 'ModulErrorHandler']);
            trigger_error($this->Translate('Invalid index'), E_USER_NOTICE);
            restore_error_handler();
            return false;
        }
        $ChildID = $this->ChildIDs[$Index];
        $Params = [
            'delay'         => $Delay,
            'desired_states'=> [
                'on' => $State
            ],
            'enable'   => true
        ];
        $ChildRequestValues = [
            'device_id'  => $ChildID,
            'requestData'=> \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::CountdownRule, '', $Params)
        ];
        $Request = \TpLink\Api\Protocol::BuildRequest(\TpLink\Api\Method::ControlChild, $this->terminalUUID, $ChildRequestValues);
        $Response = $this->SendRequest($Request);
        if ($Response === null) {
            return false;
        }
        return true;
    }

    /**
     * GetEnergyUsage
     *
     * @return false
     */
    public function GetEnergyUsage(): bool
    {
        return $this->GetEnergyUsageChilds();
    }
}
