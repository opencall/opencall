<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Model\Timezone;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Model\ClientStatus;

class ClientController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();
        $user = $this->getUser();

        // get clients
        $repo = $this->getDoctrine()->getRepository('OnCallAdminBundle:Client');
        $clients = $repo->findBy(array('user_id' => $user->getID()));

        // get role hash for menu
        $role_hash = $user->getRoleHash();

        return $this->render(
            'OnCallAdminBundle:Client:index.html.twig',
            array(
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'campaigns'),
                'clients' => $clients,
                'timezones' => Timezone::getAll(),
                'tz_selected' => '8.0',
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
        $repo = $this->getDoctrine()->getRepository('OnCallAdminBundle:Number');
        $num = $repo->find($id);
        if ($num == null)
        {
            // TODO: error message?
            return $this->redirect($this->generateUrl('oncall_admin_numbers'));
        }

        // check if we can delete
        if ($num->isInUse())
        {
            // TODO: error message?
            return $this->redirect($this->generateUrl('oncall_admin_numbers'));
        }

        // delete
        $em->remove($num);
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_numbers'));
    }
}
