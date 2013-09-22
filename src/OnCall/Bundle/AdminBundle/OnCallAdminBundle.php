<?php

namespace OnCall\Bundle\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OnCallAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
