<?php

namespace OnCall\Bundle\AdminBundle\Model;

use DateTimeZone;

class Timezone extends NamedValue
{
    static $names = array(
        '-12.0' => '(GMT -12:00) Eniwetok, Kwajalein',
        '-11.0' => '(GMT -11:00) Midway Island, Samoa',
        '-10.0' => '(GMT -10:00) Hawaii',
        '-9.0' => '(GMT -9:00) Alaska',
        '-8.0' => '(GMT -8:00) Pacific Time (US & Canada)',
        '-7.0' => '(GMT -7:00) Mountain Time (US & Canada)',
        '-6.0' => '(GMT -6:00) Central Time (US & Canada), Mexico City',
        '-5.0' => '(GMT -5:00) Eastern Time (US & Canada), Bogota, Lima',
        '-4.0' => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
        '-3.5' => '(GMT -3:30) Newfoundland',
        '-3.0' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
        '-2.0' => '(GMT -2:00) Mid-Atlantic',
        '-1.0' => '(GMT -1:00 hour) Azores, Cape Verde Islands',
        '0.0' => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
        '1.0' => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
        '2.0' => '(GMT +2:00) Kaliningrad, South Africa',
        '3.0' => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
        '3.5' => '(GMT +3:30) Tehran',
        '4.0' => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
        '4.5' => '(GMT +4:30) Kabul',
        '5.0' => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
        '5.5' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
        '6.0' => '(GMT +6:00) Almaty, Dhaka, Colombo',
        '7.0' => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
        '8.0' => '(GMT +8:00) Hong Kong, Beijing, Perth, Singapore',
        '9.0' => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
        '9.5' => '(GMT +9:30) Adelaide, Darwin',
        '10.0' => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
        '11.0' => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
        '12.0' => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka',
    );

    static $tzone = array(
        '-12.0'=>'Pacific/Kwajalein', 
        '-11.0'=>'Pacific/Samoa', 
        '-10.0'=>'Pacific/Honolulu', 
        '-9.0'=>'America/Juneau', 
        '-8.0'=>'America/Los_Angeles', 
        '-7.0'=>'America/Denver', 
        '-6.0'=>'America/Mexico_City', 
        '-5.0'=>'America/New_York', 
        '-4.0'=>'America/Caracas', 
        '-3.5'=>'America/St_Johns', 
        '-3.0'=>'America/Argentina/Buenos_Aires', 
        '-2.0'=>'Atlantic/Azores',// no cities here so just picking an hour ahead 
        '-1.0'=>'Atlantic/Azores', 
        '0.0'=>'Europe/London', 
        '1.0'=>'Europe/Paris', 
        '2.0'=>'Europe/Helsinki', 
        '3.0'=>'Europe/Moscow', 
        '3.5'=>'Asia/Tehran', 
        '4.0'=>'Asia/Baku', 
        '4.5'=>'Asia/Kabul', 
        '5.0'=>'Asia/Karachi', 
        '5.5'=>'Asia/Calcutta', 
        '6.0'=>'Asia/Colombo', 
        '7.0'=>'Asia/Bangkok', 
        '8.0'=>'Asia/Singapore', 
        '9.0'=>'Asia/Tokyo', 
        '9.5'=>'Australia/Darwin', 
        '10.0'=>'Pacific/Guam', 
        '11.0'=>'Asia/Magadan', 
        '12.0'=>'Asia/Kamchatka' 
    );

    public static function toPHPTimezone($gmt)
    {
        error_log('gmt - ' . $gmt);
        // default to gmt +8
        if (!isset(self::$tzone[$gmt]))
            return new DateTimeZone('Asia/Singapore');

        return new DateTimeZone(self::$tzone[$gmt]);
    }
}
