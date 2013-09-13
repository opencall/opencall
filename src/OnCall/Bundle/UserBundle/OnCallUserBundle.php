<?php

namespace OnCall\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OnCallUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
