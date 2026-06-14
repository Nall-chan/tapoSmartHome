<?php

declare(strict_types=1);

namespace TpLink\VariableIdent
{
    const OnOff = '\TpLink\VariableIdentOnOff';
    const Motor = '\TpLink\VariableIdentMotor';
    const Overheated = '\TpLink\VariableIdentOverheated';
    const OverheatedStatus = '\TpLink\VariableIdentOverheatedStatus';
    const Socket = '\TpLink\VariableIdentSocket';
    const Rssi = '\TpLink\VariableIdentRssi';
    const Light = '\TpLink\VariableIdentLight';
    const LightColorTemp = '\TpLink\VariableIdentLightColorTemp';
    const LightColor = '\TpLink\VariableIdentLightColor';
    const LightEffect = '\TpLink\VariableIdentLightEffect';
    const Humidity = '\TpLink\VariableIdentHumidity';
    const Online = '\TpLink\VariableIdentOnline';
    const Temp = '\TpLink\VariableIdentTemp';
    const Battery = '\TpLink\VariableIdentBattery';
    const Trv = '\TpLink\VariableIdentTrv';
    const OpenClose = '\TpLink\VariableIdentOpenClose';
}

namespace TpLink
{
    const IPSVarName = 'IPSVarName';
    const IPSVarType = 'IPSVarType';
    const IPSVarPresentationFunction = 'IPSVarPresentationFunction';
    const IPSVarPresentation = 'IPSVarPresentation';
    const PRESENTATION = 'PRESENTATION';
    const HasAction = 'HasAction';
    const ReceiveFunction = 'ReceiveFunction';
    const SendFunction = 'SendFunction';

    class VariableIdent
    {
        public static $Variables = [];
    }

    class VariableIdentOnOff extends VariableIdent
    {
        public const device_on = 'device_on';

        public static $Variables = [
            self::device_on=> [
                IPSVarName        => 'State',
                IPSVarType        => VARIABLETYPE_BOOLEAN,
                IPSVarPresentation=> [
                    PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                HasAction    => true
            ]
        ];
    }

    class VariableIdentMotor extends VariableIdent
    {
        public const motor_status = 'motor_status';
        public const current_pos = 'current_pos';
        public const target_pos = 'target_pos';
        public const motor_stuck = 'motor_stuck';
        public static $Variables = [
            self::motor_status=> [
                IPSVarName        => 'Motor Status',
                IPSVarType        => VARIABLETYPE_STRING,

                IPSVarPresentation        => [
                    PRESENTATION  => VARIABLE_PRESENTATION_ENUMERATION,
                    'ICON'        => '',
                    'LAYOUT'      => 0,
                    'DISPLAY'     => 0,
                    'OPTIONS'     => [
                        [
                            'Value'              => 'open',
                            'Caption'            => 'Open',
                            'IconActive'         => false,
                            'IconValue'          => 'angles-up',
                            'Color'              => -1
                        ],
                        [
                            'Value'              => 'pause',
                            'Caption'            => 'Stop',
                            'IconActive'         => false,
                            'IconValue'          => 'pause',
                            'Color'              => -1
                        ],

                        [
                            'Value'              => 'close',
                            'Caption'            => 'Close',
                            'IconActive'         => false,
                            'IconValue'          => 'angles-down',
                            'Color'              => -1
                        ],

                    ]
                ],
                HasAction    => true
            ],
            self::target_pos=> [
                IPSVarName        => 'Position',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION           => VARIABLE_PRESENTATION_SHUTTER,
                    'CLOSE_INSIDE_VALUE'   => 0,
                    'OPEN_OUTSIDE_VALUE'   => 100,
                    'SUN_POSITION'         => 1,
                    'USAGE_TYPE'           => 0,
                    'MAX_ROTATION_INSIDE'  => 0,
                    'MAX_ROTATION_OUTSIDE' => 0,
                ],
                HasAction    => true
            ]
        ];
    }

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

    class VariableIdentOverheated extends VariableIdent
    {
        public const overheated = 'overheated';

        public static $Variables = [
            self::overheated=> [
                IPSVarName        => 'Overheated',
                IPSVarType        => VARIABLETYPE_BOOLEAN,
                IPSVarPresentation=> [
                    PRESENTATION    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'COLOR'         => -1,
                    'ICON'          => '',
                    'CONTENT_COLOR' => -1,
                    'DISPLAY_TYPE'  => 0,
                    'OPTIONS'       => [
                        [
                            'ColorDisplay'       => -1,
                            'ContentColorDisplay'=> -1,
                            'Value'              => false,
                            'Caption'            => 'OK',
                            'IconActive'         => false,
                            'IconValue'          => '',
                            'ColorActive'        => true,
                            'ColorValue'         => -1,
                            'Color'              => -1,
                            'ContentColorActive' => false,
                            'ContentColor'       => -1
                        ],
                        [
                            'ColorDisplay'       => 16711680,
                            'ContentColorDisplay'=> -1,
                            'Value'              => true,
                            'Caption'            => 'Alarm',
                            'IconActive'         => true,
                            'IconValue'          => 'Warning',
                            'ColorActive'        => true,
                            'ColorValue'         => 16711680,
                            'ContentColorActive' => false,
                            'ContentColorValue'  => -1,
                            'Color'              => -1,
                            'ContentColor'       => -1
                        ]
                    ],
                    'PREVIEW_STYLE' => 1,
                    'SHOW_PREVIEW'  => true
                ],
                HasAction    => false
            ]
        ];
    }

    class VariableIdentOverheatedStatus extends VariableIdent
    {
        public const overheat_status = 'overheat_status';

        public static $Variables = [
            self::overheat_status=> [
                IPSVarName        => 'Overheated',
                IPSVarType        => VARIABLETYPE_BOOLEAN,
                IPSVarPresentation=> [
                    PRESENTATION    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'COLOR'         => -1,
                    'ICON'          => '',
                    'CONTENT_COLOR' => -1,
                    'DISPLAY_TYPE'  => 0,
                    'OPTIONS'       => [
                        [
                            'ColorDisplay'       => -1,
                            'ContentColorDisplay'=> -1,
                            'Value'              => false,
                            'Caption'            => 'OK',
                            'IconActive'         => false,
                            'IconValue'          => '',
                            'ColorActive'        => true,
                            'ColorValue'         => -1,
                            'Color'              => -1,
                            'ContentColorActive' => false,
                            'ContentColor'       => -1
                        ],
                        [
                            'ColorDisplay'       => 16711680,
                            'ContentColorDisplay'=> -1,
                            'Value'              => true,
                            'Caption'            => 'Alarm',
                            'IconActive'         => true,
                            'IconValue'          => 'Warning',
                            'ColorActive'        => true,
                            'ColorValue'         => 16711680,
                            'ContentColorActive' => false,
                            'ContentColorValue'  => -1,
                            'Color'              => -1,
                            'ContentColor'       => -1
                        ]
                    ],
                    'PREVIEW_STYLE' => 1,
                    'SHOW_PREVIEW'  => true
                ],
                ReceiveFunction=> 'OverheatStatusToBool',
                HasAction      => false
            ]
        ];
    }

    class VariableIdentSocket extends VariableIdent
    {
        public const on_time = 'on_time';
        public const on_time_string = 'on_time_string';
        //public const auto_off_status = 'auto_off_status';
        //public const auto_off_remain_time = 'auto_off_remain_time';

        public static $Variables = [
            self::on_time=> [
                IPSVarName        => 'On time (seconds)',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION     => VARIABLE_PRESENTATION_DURATION,
                    'COUNTDOWN_TYPE' => 0,
                    'FORMAT'         => 2,
                    'MILLISECONDS'   => false
                ],
                HasAction    => false
            ],
            /* todo
             Zeitschaltuhr zum ausschalten. Aber das ist nur der Status, Schalten fehlt noch der API Befehl :(
            self::auto_off_status=> [
                IPSVarName   => 'Auto off',
                IPSVarType   => VARIABLETYPE_STRING,
                IPSVarPresentation=> [],
                SendFunction => 'SetAutOff',
                HasAction    => true
            ],
            self::auto_off_remain_time=> [
                IPSVarName   => 'Remain time to off',
                IPSVarType   => VARIABLETYPE_INTEGER,
                IPSVarPresentation => [],
                HasAction    => true
            ],*/
        ];
    }

    class VariableIdentRssi extends VariableIdent
    {
        public const rssi = 'rssi';

        public static $Variables = [
            self::rssi => [
                IPSVarName                   => 'Rssi',
                IPSVarType                   => VARIABLETYPE_INTEGER,
                IPSVarPresentation           => [],
                HasAction                    => false
            ]
        ];
    }

    class VariableIdentLight extends VariableIdent
    {
        public const brightness = 'brightness';

        public static $Variables = [
            self::brightness=> [
                IPSVarName        => 'Brightness',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'              => 0,
                    'CUSTOM_GRADIENT'     => '[]',
                    'ICON'                => 'brightness',
                    'DECIMAL_SEPARATOR'   => '',
                    'GRADIENT_TYPE'       => 0,
                    'MAX'                 => 100,
                    'INTERVALS'           => [],
                    'INTERVALS_ACTIVE'    => false,
                    'MIN'                 => 1,
                    'PERCENTAGE'          => false,
                    'PREFIX'              => '',
                    'STEP_SIZE'           => 1,
                    'SUFFIX'              => ' %',
                    'THOUSANDS_SEPARATOR' => '',
                    'USAGE_TYPE'          => 2,
                ],
                HasAction    => true
            ]
        ];
    }
    class VariableIdentLightColorTemp extends VariableIdent
    {
        public const color_temp = 'color_temp';

        public static $Variables = [
            self::color_temp=> [
                IPSVarName        => 'Color temp',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION         => VARIABLE_PRESENTATION_SLIDER,
                    'TEMPLATE'           => VARIABLE_TEMPLATE_SLIDER_COLOR_TEMPERATURE,
                    'MIN'                => 2500,
                    'MAX'                => 6500,
                ],
                HasAction    => true
            ]
        ];
    }

    class VariableIdentLightColor extends VariableIdent
    {
        public const hue = 'hue';
        public const saturation = 'saturation';
        public const dynamic_light_effect_enable = 'dynamic_light_effect_enable';
        public const color_rgb = 'color_rgb';
        public const hsv = 'hsv';

        public static $Variables = [
            self::color_rgb=> [
                IPSVarName        => 'Color RGB',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION      => VARIABLE_PRESENTATION_COLOR,
                ],
                HasAction      => true,
                ReceiveFunction=> 'HSVtoRGB',
                SendFunction   => 'RGBtoHSV'
            ],
            self::hsv=> [
                IPSVarName        => 'Color HSV',
                IPSVarType        => VARIABLETYPE_STRING,
                IPSVarPresentation=> [
                    PRESENTATION         => VARIABLE_PRESENTATION_COLOR,
                    'COLOR_CURVE'        => 0,
                    'COLOR_SPACE'        => 1,
                    'SELECTION'          => 0,
                    'CUSTOM_COLOR_CURVE' => '[]',
                    'CUSTOM_COLOR_SPACE' => '[{"x":0.64,"y":0.33},{"x":0.3,"y":0.6},{"x":0.15,"y":0.06},{"x":0.3127,"y":0.329}]',
                    'ENCODING'           => 2,
                    'PRESET_VALUES'      => '[{"Color":16007990},{"Color":16761095},{"Color":10233776},{"Color":48340},{"Color":2201331},{"Color":15277667}]'
                ],
                HasAction      => true,
                ReceiveFunction=> 'HSVToVariable',
                SendFunction   => 'SendHSV'
            ]
        ];
    }

    class VariableIdentLightEffect extends VariableIdent
    {
        public const lighting_effect = 'lighting_effect';
        public const brightness = 'brightness'; // Overwrite LightColor
        public const color_rgb = 'color_rgb'; // Overwrite LightColor
        public const hsv = 'hsv'; // Overwrite LightColor

        public static $Variables = [
            self::lighting_effect => [
                IPSVarName                => 'Effect',
                IPSVarType                => VARIABLETYPE_STRING,
                IPSVarPresentationFunction=> 'GetEffectsForPresentation',
                IPSVarPresentation        => [
                    PRESENTATION  => VARIABLE_PRESENTATION_ENUMERATION,
                    'ICON'        => '',
                    'LAYOUT'      => 0,
                    'DISPLAY'     => 0,
                    'OPTIONS'     => []
                ],
                HasAction                 => true,
                ReceiveFunction           => 'LightEffectToVariable',
                SendFunction              => 'ActivateLightEffect'
            ],
            self::brightness=> [
                IPSVarName              => 'Brightness',
                IPSVarType              => VARIABLETYPE_INTEGER,
                IPSVarPresentation      => [
                    PRESENTATION          => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'              => 0,
                    'CUSTOM_GRADIENT'     => '[]',
                    'ICON'                => 'brightness',
                    'DECIMAL_SEPARATOR'   => '',
                    'GRADIENT_TYPE'       => 0,
                    'MAX'                 => 100,
                    'INTERVALS'           => [],
                    'INTERVALS_ACTIVE'    => false,
                    'MIN'                 => 1,
                    'PERCENTAGE'          => false,
                    'PREFIX'              => '',
                    'STEP_SIZE'           => 1,
                    'SUFFIX'              => ' %',
                    'THOUSANDS_SEPARATOR' => '',
                    'USAGE_TYPE'          => 2,
                ],
                HasAction               => true,
                ReceiveFunction         => 'BrightnessToVariable',
                SendFunction            => 'SendBrightness'
            ],
            self::color_rgb=> [
                IPSVarName        => 'Color RGB',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION      => VARIABLE_PRESENTATION_COLOR,
                ],
                HasAction      => true,
                ReceiveFunction=> 'HSVtoRGB',
                SendFunction   => 'RGBtoHSV'
            ],
            self::hsv=> [
                IPSVarName        => 'Color HSV',
                IPSVarType        => VARIABLETYPE_STRING,
                IPSVarPresentation=> [
                    PRESENTATION         => VARIABLE_PRESENTATION_COLOR,
                    'COLOR_CURVE'        => 0,
                    'COLOR_SPACE'        => 1,
                    'SELECTION'          => 0,
                    'CUSTOM_COLOR_CURVE' => '[]',
                    'CUSTOM_COLOR_SPACE' => '[{"x":0.64,"y":0.33},{"x":0.3,"y":0.6},{"x":0.15,"y":0.06},{"x":0.3127,"y":0.329}]',
                    'ENCODING'           => 2,
                    'PRESET_VALUES'      => '[{"Color":16007990},{"Color":16761095},{"Color":10233776},{"Color":48340},{"Color":2201331},{"Color":15277667}]'
                ],
                HasAction      => true,
                ReceiveFunction=> 'HSVToVariable',
                SendFunction   => 'SendHSV'
            ]
        ];
    }

    class VariableIdentOnline extends VariableIdent
    {
        public const status = 'status';

        public static $Variables = [
            self::status => [
                IPSVarName                   => 'Online status',
                IPSVarType                   => VARIABLETYPE_STRING,
                IPSVarPresentation           => [],
                HasAction                    => false
            ]
        ];
    }

    class VariableIdentHumidity extends VariableIdent
    {
        public const current_humidity = 'current_humidity';

        public static $Variables = [
            self::current_humidity=> [
                IPSVarName        => 'Current humidity',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION          => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'COLOR'               => -1,
                    'DECIMAL_SEPARATOR'   => '',
                    'DIGITS'              => 0,
                    'ICON'                => '',
                    'INTERVALS'           => [],
                    'INTERVALS_ACTIVE'    => false,
                    'MAX'                 => 100,
                    'MIN'                 => 0,
                    'MULTILINE'           => false,
                    'PERCENTAGE'          => true,
                    'PREFIX'              => '',
                    'SUFFIX'              => ' %',
                    'THOUSANDS_SEPARATOR' => '',
                    'USAGE_TYPE'          => 0,
                ],
                HasAction    => false
            ]
        ];
    }

    class VariableIdentTemp extends VariableIdent
    {
        public const current_temp = 'current_temp';

        public static $Variables = [
            self::current_temp=> [
                IPSVarName         => 'Current temperature',
                IPSVarType         => VARIABLETYPE_FLOAT,
                IPSVarPresentation => [
                    PRESENTATION   => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'TEMPLATE'     => VARIABLE_TEMPLATE_VALUE_PRESENTATION_ROOM_TEMPERATURE
                ],
                HasAction    => false
            ]
        ];
    }

    class VariableIdentBattery extends VariableIdent
    {
        public const at_low_battery = 'at_low_battery';
        public const battery_percentage = 'battery_percentage';

        public static $Variables = [
            self::at_low_battery=> [
                IPSVarName        => 'Low battery',
                IPSVarType        => VARIABLETYPE_BOOLEAN,
                IPSVarPresentation=> [
                    PRESENTATION            => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'MIN'                   => 0,
                    'DIGITS'                => 0,
                    'MULTILINE'             => false,
                    'ICON'                  => 'Battery',
                    'INTERVALS_ACTIVE'      => true,
                    'MAX'                   => 1,
                    'PERCENTAGE'            => false,
                    'OPTIONS'               => [
                        [
                            'Value'      => false,
                            'Caption'    => 'OK',
                            'IconActive' => false,
                            'IconValue'  => '',
                            'ColorActive'=> true,
                            'ColorValue' => -1
                        ],
                        [
                            'Value'      => true,
                            'Caption'    => 'Low battery',
                            'IconActive' => false,
                            'IconValue'  => '',
                            'ColorActive'=> true,
                            'ColorValue' => 16711680
                        ]
                    ],
                    'PREFIX'                => '',
                    'SUFFIX'                => '',
                    'USAGE_TYPE'            => 0,
                ],
                HasAction    => false
            ],
            self::battery_percentage=> [
                IPSVarName        => 'Battery',
                IPSVarType        => VARIABLETYPE_INTEGER,
                IPSVarPresentation=> [
                    PRESENTATION       => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'MIN'              => 0,
                    'DIGITS'           => 0,
                    'MULTILINE'        => false,
                    'ICON'             => 'Battery',
                    'MAX'              => 100,
                    'INTERVALS'        => [],
                    'INTERVALS_ACTIVE' => false,
                    'PERCENTAGE'       => true,
                    'PREFIX'           => '',
                    'SUFFIX'           => ' %',
                    'USAGE_TYPE'       => 0
                ],
                HasAction    => false
            ]
        ];
    }

    class VariableIdentTrv extends VariableIdent
    {
        public const target_temp = 'target_temp';
        public const frost_protection_on = 'frost_protection_on';
        public const child_protection = 'child_protection';
        public const trv_states = 'trv_states';

        public static $Variables = [
            self::target_temp=> [
                IPSVarName        => 'Setpoint temperature',
                IPSVarType        => VARIABLETYPE_FLOAT,
                IPSVarPresentation=> [
                    'PRESENTATION'        => VARIABLE_PRESENTATION_SLIDER,
                    'DIGITS'              => 1,
                    'CUSTOM_GRADIENT'     => '[]',
                    'ICON'                => 'temperature-half',
                    'DECIMAL_SEPARATOR'   => 'Client',
                    'GRADIENT_TYPE'       => 1,
                    'MAX'                 => 30,
                    'INTERVALS'           => [],
                    'INTERVALS_ACTIVE'    => false,
                    'MIN'                 => 5,
                    'PERCENTAGE'          => false,
                    'PREFIX'              => '',
                    'STEP_SIZE'           => 0.5,
                    'SUFFIX'              => ' °C',
                    'THOUSANDS_SEPARATOR' => '',
                    'USAGE_TYPE'          => 0,
                ],
                HasAction         => true
            ],
            self::frost_protection_on=> [
                IPSVarName        => 'Frost protection',
                IPSVarType        => VARIABLETYPE_BOOLEAN,
                IPSVarPresentation=> [
                    PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                HasAction      => true
            ],
            self::child_protection=> [
                IPSVarName        => 'Child Protection',
                IPSVarType        => VARIABLETYPE_BOOLEAN,
                IPSVarPresentation=> [
                    PRESENTATION => VARIABLE_PRESENTATION_SWITCH
                ],
                HasAction      => true
            ],
            self::trv_states=> [
                IPSVarName          => 'State',
                IPSVarType          => VARIABLETYPE_STRING,
                IPSVarPresentation  => [],
                HasAction           => false,
                ReceiveFunction     => 'TrvStateToString',
            ],

        ];
    }
    class VariableIdentOpenClose extends VariableIdent
    {
        public const open = 'open';
        public static $Variables = [
            self::open=> [
                IPSVarName        => 'State',
                IPSVarType        => VARIABLETYPE_BOOLEAN,
                IPSVarPresentation=> [
                    PRESENTATION   => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'OPTIONS'      => [
                        [
                            'Value'       => false,
                            'Caption'     => 'Closed',
                            'IconActive'  => true,
                            'IconValue'   => 'window-left-closed-right-closed',
                            'ColorActive' => true,
                            'ColorValue'  => -1
                        ],
                        [
                            'Value'       => true,
                            'Caption'     => 'Opened',
                            'IconActive'  => true,
                            'IconValue'   => 'window-left-open-right-open',
                            'ColorActive' => true,
                            'ColorValue'  => 0x0000ff
                        ]

                    ]
                ],
                HasAction    => false
            ]
        ];
    }
    /*
        // channel group alarm
        public static final String CHANNEL_GROUP_ALARM = "alarm";
        public static final String CHANNEL_ALARM_ACTIVE = "alarmActive";
        public static final String CHANNEL_ALARM_SOURCE = "alarmSource";
        public static final String CHANNEL_GROUP_SENSOR = "sensor";
        public static final String CHANNEL_IS_OPEN = "isOpen";
        public static final String CHANNEL_TEMPERATURE = "currentTemp";
        public static final String CHANNEL_HUMIDITY = "currentHumidity";
        // hub child events
        public static final String EVENT_BATTERY_LOW = "batteryIsLow";
        public static final String EVENT_CONTACT_OPENED = "contactOpened";
        public static final String EVENT_CONTACT_CLOSED = "contactClosed";
        public static final String EVENT_STATE_BATTERY_LOW = "batteryLow";
        public static final String EVENT_STATE_OPENED = "open";
        public static final String EVENT_STATE_CLOSED = "closed";
     */
    class VariableProfile
    {
        public const Runtime = 'Tapo.Runtime';
        public const RuntimeSeconds = 'Tapo.RuntimeSeconds';
        public const ColorTemp = 'Tapo.ColorTemp';
        public const Brightness = 'Tapo.Brightness';
        public const LightingEffect = 'Tapo.LightingEffect.%d';
        public const TargetTemperature = 'Tapo.Temperature.Room';
    }

    class KelvinTable
    {
        private static $Table = [
            2500=> [255, 161, 72],
            2600=> [255, 165, 79],
            2700=> [255, 169, 87],
            2800=> [255, 173, 94],
            2900=> [255, 177, 101],
            3000=> [255, 180, 107],
            3100=> [255, 184, 114],
            3200=> [255, 187, 120],
            3300=> [255, 190, 126],
            3400=> [255, 193, 132],
            3500=> [255, 196, 137],
            3600=> [255, 199, 143],
            3700=> [255, 201, 148],
            3800=> [255, 204, 153],
            3900=> [255, 206, 159],
            4000=> [255, 209, 163],
            4100=> [255, 211, 168],
            4200=> [255, 213, 173],
            4300=> [255, 215, 177],
            4400=> [255, 217, 182],
            4500=> [255, 219, 186],
            4600=> [255, 221, 190],
            4700=> [255, 223, 194],
            4800=> [255, 225, 198],
            4900=> [255, 227, 202],
            5000=> [255, 228, 206],
            5100=> [255, 230, 210],
            5200=> [255, 232, 213],
            5300=> [255, 233, 217],
            5400=> [255, 235, 220],
            5500=> [255, 236, 224],
            5600=> [255, 238, 227],
            5700=> [255, 239, 230],
            5800=> [255, 240, 233],
            5900=> [255, 242, 236],
            6000=> [255, 243, 239],
            6100=> [255, 244, 242],
            6200=> [255, 245, 245],
            6300=> [255, 246, 247],
            6400=> [255, 248, 251],
            6500=> [255, 249, 253]
        ];

        /**
         * ToRGB
         *
         * @param  int $Kelvin
         * @return array
         */
        public static function ToRGB(int $Kelvin): array
        {
            foreach (self::$Table as $Key => $RGB) {
                if ($Key < $Kelvin) {
                    continue;
                }
                break;
            }
            return $RGB;
        }
    }
}
