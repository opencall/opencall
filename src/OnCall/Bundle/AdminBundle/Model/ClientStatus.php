<?php

namespace OnCall\Bundle\AdminBundle\Model;

class ClientStatus extends NamedValue
{
    const INACTIVE          = 0;
    const ACTIVE            = 1;

    static $names = array(
        0 => 'Inactive',
        1 => 'Active',
    );
}
