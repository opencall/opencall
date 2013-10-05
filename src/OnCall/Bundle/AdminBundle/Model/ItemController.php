<?php

namespace OnCall\Bundle\AdminBundle\Model;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use OnCall\Bundle\AdminBundle\Model\ItemStatus;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;
use DateTime;

abstract class ItemController extends Controller
{
    protected $name;
    protected $top_color;
    protected $agg_type;
    protected $parent_repo;
    protected $child_fetch_method;

    public function __construct()
    {
        $this->name = 'Item';
        $this->top_color = 'blue';
        $this->agg_type = array();
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

        // get parent
        $parent = $this->getDoctrine()
            ->getRepository($this->parent_repo)
            ->find($item_id);

        // not found
        if ($parent == null)
            throw new AccessDeniedException();

        // children
        $method = $this->child_fetch_method;
        $children = $parent->$method();
        $child_ids = array();
        foreach ($children as $child)
            $child_ids[] = $child->getID();

        // make sure the user is the account holder
        if ($user->getID() != $parent->getUser()->getID())
            throw new AccessDeniedException();

        return array(
            'parent' => $parent,
            'children' => $children,
            'child_ids' => $child_ids
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
}
