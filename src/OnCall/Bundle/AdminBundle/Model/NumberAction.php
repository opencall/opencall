<?php

namespace OnCall\Bundle\AdminBundle\Model;

class NumberAction extends NamedValue
{
    static $names = array(
        1 => 'create',
        2 => 'assign',
        3 => 'unassign',
        4 => 'delete'
    );
}
