<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OnCall\Bundle\AdminBundle\Model\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Model\ClientStatus;

class CampaignController extends Controller
{
    public function indexAction($cid)
    {
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();
        $user = $this->getUser();

        // get role hash for menu
        $role_hash = $user->getRoleHash();

        // get client
        $client = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client')
            ->find($cid);

        // not found
        if ($client == null)
            throw new AccessDeniedException();

        // make sure the user is the account holder
        if ($user->getID() != $client->getUser()->getID())
            throw new AccessDeniedException();

        // campaigns
        $campaigns = array();


        return $this->render(
            'OnCallAdminBundle:Campaign:index.html.twig',
            array(
                'user' => $user,
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'campaigns'),
                'client' => $client,
                'campaigns' => $campaigns,
            )
        );
    }

    protected function updateClient(Client $client, $data)
    {
        // TODO: cleanup parameters / default value
        $name = trim($data['name']);
        $timezone = $data['timezone'];

        $client->setName($name)
            ->setTimezone($timezone);

        if (isset($data['status']))
        {
            // TODO: check valid status
            $status = $data['status'];
            $client->setStatus($status);
        }

        if (isset($data['user']))
            $client->setUser($user);
    }

    public function createAction()
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();

        $client = new Client();
        $data['user'] = $this->getUser();
        $data['status'] = ClientStatus::ACTIVE;
        $this->updateClient($client, $data);

        $em->persist($client);
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_clients'));
    }

    public function getAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('OnCallAdminBundle:Client');
        $client = $repo->find($id);
        if ($client == null)
            return new Response('');

        return new Response($client->jsonify());
    }

    public function updateAction($id)
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();

        // find
        $repo = $this->getDoctrine()->getRepository('OnCallAdminBundle:Client');
        $client = $repo->find($id);
        if ($client == null)
        {
            // TODO: error message?
            return $this->redirect($this->generateUrl('oncall_admin_clients'));
        }

        // update
        $this->updateClient($client, $data);
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_clients'));
    }

    public function assignAction($acc_id)
    {
        $em = $this->getDoctrine()->getManager();

        // find user
        $mgr = $this->get('fos_user.user_manager');
        $user = $mgr->findUserBy(array('id' => $acc_id));

        // no user found
        if ($user == null)
        {
            // TODO: error message?
            return $this->redirect($this->generateUrl('oncall_admin_numbers'));
        }

        // get the numbers
        $num_ids = $this->getRequest()->request->get('number_ids');
        if ($num_ids == null || !is_array($num_ids))
        {
            // TODO: error message?
            return $this->redirect($this->generateUrl('oncall_admin_numbers'));
        }

        // iterate through all numbers checked
        foreach ($num_ids as $num)
        {
            // find number
            $repo = $this->getDoctrine()->getRepository('OnCallAdminBundle:Number');
            $num_object = $repo->find($num);
            if ($num_object == null)
            {
                continue;
            }

            // TODO: check if we can assign

            // TODO: log number assignment

            // assign
            $num_object->setUser($user);
        }

        // flush db
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_numbers'));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // find
        $client = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client')
            ->find($id);
        if ($client == null)
        {
            // TODO: error message?
            return $this->redirect($this->generateUrl('oncall_admin_clients'));
        }

        // set inactive
        $client->setStatus(ClientStatus::INACTIVE);
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_clients'));
    }
}
