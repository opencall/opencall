<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;

class MainController extends Controller
{
    public function indexAction()
    {
        $role_hash = $this->getUser()->getRoleHash();

        // check if admin
        if (isset($role_hash['ROLE_ADMIN']))
            return $this->redirect($this->generateUrl('oncall_admin_accounts'));

        // everyone else
        return $this->redirect($this->generateUrl('oncall_admin_clients'));
    }
}
