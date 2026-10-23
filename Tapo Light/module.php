<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/libs/TapoDevice.php';

/**
 * TapoLight Klasse für die Anbindung von WiFi Bulbs & Strips.
 * Erweitert \TpLink\Device.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024-2026 Michael Tröger
 * @license       Custom Non-Commercial Share-Alike License (see LICENSE)
 *
 * @version       2.23
 */
class TapoLight extends \TpLink\Device
{
    protected static $ModuleIdents = [
        \TpLink\VariableIdent\OnOff,
        \TpLink\VariableIdent\Overheated,
        \TpLink\VariableIdent\Rssi,
        \TpLink\VariableIdent\Light
    ];

    /**
     * ApplyChanges
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        $this->UnregisterProfile(\TpLink\VariableProfile::Brightness);
        //Never delete this line!
        parent::ApplyChanges();
    }
}
