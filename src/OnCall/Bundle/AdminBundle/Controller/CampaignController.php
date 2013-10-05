<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use OnCall\Bundle\AdminBundle\Model\ItemController;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Entity\Campaign;
use OnCall\Bundle\AdminBundle\Model\ItemStatus;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class CampaignController extends ItemController
{
    public function __construct()
    {
        $this->name = 'Campaign';
        $this->top_color = 'blue';
        $this->agg_type = array(
            'parent' => AggregateFilter::TYPE_CLIENT,
            'table' => AggregateFilter::TYPE_CLIENT_CHILDREN,
            'daily' => AggregateFilter::TYPE_DAILY_CLIENT,
            'hourly' => AggregateFilter::TYPE_HOURLY_CLIENT
        );

        $this->parent_repo = 'OnCallAdminBundle:Client';
        $this->child_fetch_method = 'getCampaigns';
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
