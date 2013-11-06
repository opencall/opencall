<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use OnCall\Bundle\AdminBundle\Model\Controller;
use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Model\Timezone;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Model\ClientStatus;
use Plivo\AccountCounter\Repository as ACRepo;
use Plivo\AccountCounter\Entry as ACEntry;
use DateTime;

class ClientController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();
        $user = $this->getUser();

        // get role hash for menu
        $role_hash = $user->getRoleHash();

        // get clients
        $repo = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client');

        // for admin switched user
        if ($this->get('security.context')->isGranted('ROLE_PREVIOUS_ADMIN'))
        {
            $clients = $repo->findBy(array(
                'user_id' => $user->getID()
            ));
        }
        // for normal users
        else
        {
            $clients = $repo->findBy(array(
                'user_id' => $user->getID(),
                'status' => ClientStatus::ACTIVE
            ));
        }

        // get client ids
        $client_ids = array();
        foreach ($clients as $cli)
            $client_ids[] = $cli->getID();

        // get counter summaries
        if (count($client_ids) > 0)
        {
            $summaries = $this->getDoctrine()
                ->getRepository('OnCallAdminBundle:Counter')
                ->findClientSummaries($client_ids);
        }
        else
            $summaries = array('day' => array(), 'month' => array());


        return $this->render(
            'OnCallAdminBundle:Client:index.html.twig',
            array(
                'user' => $user,
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'campaigns'),
                'clients' => $clients,
                'timezones' => Timezone::getAll(),
                'tz_selected' => '8.0',
                'summ_day' => $summaries['day'],
                'summ_month' => $summaries['month'],
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
            $client->setUser($data['user']);
    }

    public function createAction()
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->getUser();

        $client = new Client();
        $data['user'] = $user;
        $data['status'] = ClientStatus::ACTIVE;
        $this->updateClient($client, $data);

        $em->persist($client);
        $em->flush();

        // add account counter code
        $conn = $this->get('database_connection');
        $ac_repo = new ACRepo($conn->getWrappedConnection());
        $ac_entry = new ACEntry(new DateTime(), $user->getID());
        $ac_entry->setClient(1);
        $ac_repo->append($ac_entry);

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

        // set children (campaigns, ad group, advert) inactive
        $camps = $client->getCampaigns();
        foreach ($camps as $camp)
            $camp->setInactive();

        // set numbers inactive
        $numbers = $client->getNumbers();
        foreach ($numbers as $num)
            $num->unassign();

        // flush
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_clients'));
    }
}
