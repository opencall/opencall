<?php

namespace OnCall\Bundle\AdminBundle\Model;

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
}
