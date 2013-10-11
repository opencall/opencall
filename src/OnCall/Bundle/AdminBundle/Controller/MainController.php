<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;

class MainController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();

        // admin
        if (isset($role_hash['ROLE_ADMIN']))
            return $this->redirect($this->generateUrl('oncall_admin_accounts'));

        // multi-client account
        if ($user->isMultiClient())
            return $this->redirect($this->generateUrl('oncall_admin_clients'));

        // single client account
        $user_id = $user->getID();
        $client = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client')
            ->findFirst($user_id);

        // no client? (should never reach this)
        if ($client == null)
        {
            // TODO: handle error by creating client automatically?
            return $this->redirect($this->generateUrl('oncall_admin_clients'));
        }

        return $this->redirect(
            $this->generateUrl(
                'oncall_admin_campaigns',
                array('id' => $client->getID())
            )
        );
    }
}
