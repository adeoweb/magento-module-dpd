<?php

namespace AdeoWeb\Dpd\Helper;

class Config
{
    const TYPE_METHOD = 'method';
    const TYPE_METHOD_JS_COMPONENTS = 'method_js_components';
    const TYPE_CLASSIC_DELIVERY_TIME = 'classic_delivery_time';
    const TYPE_CLASSIC_DELIVERY_TIME_CITY = 'classic_delivery_time_city';
    const TYPE_AVAILABLE_TIMES = 'available_times';

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|string|null
     */
    public function getCode($type, $code = '')
    {
        $codes = $this->getCodes();

        if (!isset($codes[$type])) {
            return null;
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return null;
        }

        return $codes[$type][$code];
    }

    /**
     * Get configuration data of carrier
     *
     * @return array
     */
    protected function getCodes()
    {
        return [
            self::TYPE_METHOD => [
                'classic' => __('DPD - Classic'),
                'pickup' => __('DPD - Pickup'),
                'saturday' => __('DPD - Saturday')
            ],
            self::TYPE_METHOD_JS_COMPONENTS => [
                'classic' => [
                    'delivery-time' => 'AdeoWeb_Dpd/js/view/checkout/shipping/delivery-time'
                ],
                'pickup' => ['AdeoWeb_Dpd/js/view/checkout/shipping/pickup-point']
            ],
            self::TYPE_CLASSIC_DELIVERY_TIME => [
                '1' => '8:00 - 14:00',
                '2' => '8:00 - 18:00',
                '3' => '9:00 - 13:00',
                '4' => '14:00 - 17:00',
                '5' => '14:00 - 18:00',
                '6' => '16:00 - 18:00',
                '7' => '18:00 - 22:00'
            ],
            self::TYPE_CLASSIC_DELIVERY_TIME_CITY => [
                'Vilnius' => ['2', '1', '5', '7'],
                'Kaunas' => ['2', '1', '5', '7'],
                'Klaipėda' => ['2', '1', '5', '7'],
                'Klaipeda' => ['2', '1', '5', '7'],
                'Šiauliai' => ['2', '1', '5', '7'],
                'Siauliai' => ['2', '1', '5', '7'],
                'Panevėžys' => ['2', '1', '5', '7'],
                'Panevezys' => ['2', '1', '5', '7'],
                'Utena' => ['2', '1', '5', '7'],
                'Telšiai' => ['2', '1', '5', '7'],
                'Telsiai' => ['2', '1', '5', '7'],
                'Tauragė' => ['2', '1', '5', '7'],
                'Taurage' => ['2', '1', '5', '7'],
                'Alytus' => ['2', '1', '5', '7'],
                'Marijampolė' => ['2', '1', '5', '7'],
                'Marijampole' => ['2', '1', '5', '7'],
                'Rīga' => ['2', '7'],
                'Riga' => ['2', '7'],
                'Jelgava' => ['2', '7'],
                'Jēkabpils' => ['2', '7'],
                'Jekabpils' => ['2', '7'],
                'Daugavpils' => ['2', '7'],
                'Saldus' => ['2', '7'],
                'Liepāja' => ['2', '7'],
                'Liepaja' => ['2', '7'],
                'Talsi' => ['2', '7'],
                'Ventspils' => ['2', '7'],
                'Valmiera' => ['2', '7'],
                'Cēsis' => ['2', '7'],
                'Cesis' => ['2', '7'],
                'Gulbene' => ['2', '7'],
                'Tallinn' => ['3', '6', '4']
            ],
            self::TYPE_AVAILABLE_TIMES => [
                '00:00' => '00:00',
                '00:30' => '00:30',
                '01:00' => '01:00',
                '01:30' => '01:30',
                '02:00' => '02:00',
                '02:30' => '02:30',
                '03:00' => '03:00',
                '03:30' => '03:30',
                '04:00' => '04:00',
                '04:30' => '04:30',
                '05:00' => '05:00',
                '05:30' => '05:30',
                '06:00' => '06:00',
                '06:30' => '06:30',
                '07:00' => '07:00',
                '07:30' => '07:30',
                '08:00' => '08:00',
                '08:30' => '08:30',
                '09:00' => '09:00',
                '09:30' => '09:30',
                '10:00' => '10:00',
                '10:30' => '10:30',
                '11:00' => '11:00',
                '11:30' => '11:30',
                '12:00' => '12:00',
                '12:30' => '12:30',
                '13:00' => '13:00',
                '13:30' => '13:30',
                '14:00' => '14:00',
                '14:30' => '14:30',
                '15:00' => '15:00',
                '15:30' => '15:30',
                '16:00' => '16:00',
                '16:30' => '16:30',
                '17:00' => '17:00',
                '17:30' => '17:30',
                '18:00' => '18:00',
                '18:30' => '18:30',
                '19:00' => '19:00',
                '19:30' => '19:30',
                '20:00' => '20:00',
                '20:30' => '20:30',
                '21:00' => '21:00',
                '21:30' => '21:30',
                '22:00' => '22:00',
                '22:30' => '22:30',
                '23:00' => '23:00',
                '23:30' => '23:30',
            ]
        ];
    }
}