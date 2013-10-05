<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OnCall\Bundle\AdminBundle\Model\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Entity\Campaign;
use OnCall\Bundle\AdminBundle\Model\ItemStatus;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;
use Symfony\Component\Security\Core\SecurityContextInterface;
use DateTime;

class CampaignController extends Controller
{
    protected $name;
    protected $top_color;
    protected $agg_type;

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
    }

    public function indexAction($id)
    {
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();

        // fetch needed data
        $fetch_res = $this->fetchAll($id);

        // aggregates
        $agg = $this->processAggregates($id, $fetch_res['child_ids']);

        return $this->render(
            'OnCallAdminBundle:Campaign:index.html.twig',
            array(
                'user' => $user,
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'campaigns'),
                'parent' => $fetch_res['parent'],
                'agg_parent' => $agg['parent'],
                'agg_table' => $agg['table'],
                'agg_filter' => new AggregateFilter($this->agg_type['parent'], $id),
                'daily' => $agg['daily'],
                'hourly' => $agg['hourly'],
                'children' => $fetch_res['children'],
                'top_color' => $this->top_color,
                'name' => $this->name,
            )
        );
    }

    protected function fetchAll($item_id)
    {
        $user = $this->getUser();

        // get client
        $client = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client')
            ->find($item_id);

        // not found
        if ($client == null)
            throw new AccessDeniedException();

        // campaigns
        $campaigns = $client->getCampaigns();
        $camp_ids = array();
        foreach ($campaigns as $camp)
            $camp_ids[] = $camp->getID();

        // make sure the user is the account holder
        if ($user->getID() != $client->getUser()->getID())
            throw new AccessDeniedException();

        return array(
            'parent' => $client,
            'children' => $campaigns,
            'child_ids' => $camp_ids
        );
    }

    protected function processAggregates($pid, $child_ids)
    {
        // counter repo
        $count_repo = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Counter');

        // aggregate top level, table, daily, and hourly
        $filter = new AggregateFilter($this->agg_type['parent'], $pid);
        $tfilter = new AggregateFilter($this->agg_type['table'], $pid);
        $dfilter = new AggregateFilter($this->agg_type['daily'], $pid);
        $hfilter = new AggregateFilter($this->agg_type['hourly'], $pid);

        // check for specified dates
        $query = $this->getRequest()->query;
        $date_from = $query->get('date_from');
        $date_to = $query->get('date_to');
        if ($date_from != null)
        {
            $filter->setDateFrom(new DateTime($date_from));
            $tfilter->setDateFrom(new DateTime($date_from));
            $dfilter->setDateFrom(new DateTime($date_from));
            $hfilter->setDateFrom(new DateTime($date_from));
        }
        if ($date_to != null)
        {
            $filter->setDateTo(new DateTime($date_to));
            $tfilter->setDateTo(new DateTime($date_to));
            $dfilter->setDateTo(new DateTime($date_to));
            $hfilter->setDateTo(new DateTime($date_to));
        }

        // get aggregate data for parent 
        $agg_parent = $count_repo->findItemAggregate($filter);
        $agg_table = $count_repo->findItemAggregate($tfilter, $child_ids);
        $agg_daily = $count_repo->findChartAggregate($dfilter);
        $agg_hourly = $count_repo->findChartAggregate($hfilter);

        // separate daily and monthly data
        $daily = $this->separateChartData($agg_daily);
        $hourly = $this->separateChartData($agg_hourly);

        return array(
            'parent' => $agg_parent,
            'table' => $agg_table,
            'daily' => $daily,
            'hourly' => $hourly
        );
    }

    protected function separateChartData($agg)
    {
        $chart['total'] = array();
        $chart['failed'] = array();
        $chart['plead'] = array();
        foreach ($agg as $adata)
        {
            $chart['total'][] = $adata->getTotal();
            $chart['failed'][] = $adata->getFailed();
            $chart['plead'][] = $adata->getPLead();
        }

        return $chart;
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
