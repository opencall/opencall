<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OnCall\Bundle\AdminBundle\Model\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Entity\Campaign;
use OnCall\Bundle\AdminBundle\Model\ItemStatus;
use DateTime;

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

        // counter repo
        $count_repo = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Counter');

        // process dates
        // TODO: use date parameters, fallback to default 7 days if not specified
        $date_now = new DateTime();
        $date_now_text = $date_now->format('Y-m-d H') . ':00:00';
        $date_to = DateTime::createFromFormat('Y-m-d H:i:s', $date_now_text);
        $date_from = DateTime::createFromFormat('Y-m-d H:i:s', $date_now_text);
        $date_from->modify('-7 day');

        // get aggregate data for client
        $agg_client = $count_repo->findAggregateSingle('client', $cid, $date_from, $date_to);

        // make sure the user is the account holder
        if ($user->getID() != $client->getUser()->getID())
            throw new AccessDeniedException();

        // campaigns
        $campaigns = $client->getCampaigns();

        return $this->render(
            'OnCallAdminBundle:Campaign:index.html.twig',
            array(
                'user' => $user,
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'campaigns'),
                'client' => $client,
                'agg_client' => $agg_client,
                'campaigns' => $campaigns,
            )
        );
    }

    protected function update(Campaign $campaign, $data)
    {
        // TODO: cleanup parameters / default value
        $name = trim($data['name']);

        $campaign->setName($name);

        if (isset($data['status']))
        {
            $status = $data['status'];
            $campaign->setStatus($status);
        }

        if (isset($data['client']))
            $campaign->setClient($data['client']);
    }

    public function createAction($cid)
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();

        // find client
        $client = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client')
            ->find($cid);

        // not found
        if ($client == null)
        {
            $this->addFlash('error', 'Could not find client.');
            return $this->redirect($this->generateUrl('/'));
        }

        $camp = new Campaign();
        $data['client'] = $client;
        $data['status'] = ItemStatus::ACTIVE;
        $this->update($camp, $data);

        $em->persist($camp);
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_campaigns', array('cid' => $cid)));
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
