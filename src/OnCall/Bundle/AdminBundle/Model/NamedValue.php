<?php

namespace OnCall\Bundle\AdminBundle\Model;

abstract class NamedValue
{
    public static function isValid($type)
    {
        if (isset(static::$names[$type]))
            return true;

        return false;
    }

    public static function getName($value)
    {
        if (!static::isValid($value))
            return 'Unknown';

        return static::$names[$value];
    }
}
