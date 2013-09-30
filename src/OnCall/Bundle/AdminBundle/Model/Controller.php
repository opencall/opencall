<?php

namespace OnCall\Bundle\AdminBundle\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{
    public function addFlash($type, $message)
    {
        $this->get('session')
            ->getFlashBag()
            ->add($type, $message);

        return $this;
    }
}
