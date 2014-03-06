<?php

namespace Plivo;

/*
NOTE: we only support the following countries for now:
Hong Kong
Singapore
*/
class NumberFormatter
{
    public function __construct()
    {
    }

    public function clean($raw_num)
    {
        return preg_replace('/[^0-9]/', '', $raw_num);
    }

    protected function parseFour($num)
    {
        $ccode = substr($num, 0, 4);
        switch ($ccode)
        {
        }

        return null;
    }

    protected function parseThree($num)
    {
        $ccode = substr($num, 0, 3);
        switch ($ccode)
        {
            // hong kong
            case '852':
                return '852';
        }

        return null;
    }

    protected function parseTwo($num)
    {
        $ccode = substr($num, 0, 2);
        switch ($ccode)
        {
            // singapore
            case '65':
                return '65';
            // australia
            case '61':
                return '61';
        }

        return null;
    }
    
    protected function parseOne($num)
    {
        $ccode = substr($num, 0, 1);
        switch ($ccode)
        {
        }

        return null;
    }

    public function parseCountryCode($num)
    {
        // figure out the country code

        // check 4 digits
        $ccode = $this->parseFour($num);
        if ($ccode != null)
            return $ccode;

        // check 3 digits
        $ccode = $this->parseThree($num);
        if ($ccode != null)
            return $ccode;

        // check 2 digits
        $ccode = $this->parseTwo($num);
        if ($ccode != null)
            return $ccode;

        // check 1 digit
        $ccode = $this->parseOne($num);
        if ($ccode != null)
            return $ccode;

        return null;
    }

    public function getRegex($ccode)
    {
        switch ($ccode)
        {
            // hong kong
            case '852':
                return array(
                    'pattern' => '/(852)(\\d{4})(\\d+)/',
                    'format' => '+$1 $2 $3'
                );

            // singapore
            case '65':
                return array(
                    'pattern' => '/(65)(\\d{4})(\\d+)/',
                    'format' => '+$1 $2 $3'
                );

            // australia
            case '61':
                return array(
                    'pattern' => '/(61)(\\d+)/',
                    'format' => '+$1 $2'
                );
        }

        return null;
    }

    public function format($raw_number)
    {
        $num = $this->clean($raw_number);
        $ccode = $this->parseCountryCode($num);
        if ($ccode == null)
            return '+' . $num;

        $regex = $this->getRegex($ccode);
        if ($regex == null)
            return '+' . $num;

        return preg_replace($regex['pattern'], $regex['format'], $num);
    }
}
