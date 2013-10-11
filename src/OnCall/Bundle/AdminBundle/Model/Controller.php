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

    public function getClientID()
    {
        // get session client id
        $sess = $this->getRequest()->getSession();
        if (!$sess->has('client_id'))
            return null;

        return $sess->get('client_id');
    }

    public function getClient()
    {
        $client_id = $this->getClientID();
        if ($client_id == null)
            return null;

        return $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client')
            ->find($client_id);
    }
}
