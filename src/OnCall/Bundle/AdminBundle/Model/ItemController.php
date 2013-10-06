<?php

namespace OnCall\Bundle\AdminBundle\Model;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use OnCall\Bundle\AdminBundle\Entity\Item;
use DateTime;

abstract class ItemController extends Controller
{
    protected $name;
    protected $top_color;
    protected $agg_type;

    protected $parent_repo;
    protected $child_repo;
    protected $child_fetch_method;

    protected $url_child;
    protected $url_parent;

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
                'url_child' => $this->url_child
            )
        );
    }

    public function createAction($id)
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();

        // find
        $parent = $this->findParent($id);

        // create
        $item_class = '\OnCall\Bundle\AdminBundle\Entity\\' . $this->name;
        $item = new $item_class();
        $data['parent'] = $parent;
        $data['status'] = ItemStatus::ACTIVE;
        $this->update($item, $data);
        $em->persist($item);
        $em->flush();

        // success
        $this->addFlash('success', $this->name . ' ' . $item->getName() . ' has been added.');

        return $this->redirect($this->generateUrl($this->url_parent, array('id' => $id)));
    }

    public function getAction($id)
    {
        $child = $this->findChild($id);

        return new Response($child->jsonify());
    }

    public function updateAction($id)
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();

        // find
        $child = $this->findChild($id);

        // update
        $this->update($child, $data);
        $em->flush();

        // success
        $this->addFlash('success', $this->name . ' ' . $child->getName() . ' has been updated.');

        return $this->redirect($this->generateUrl($this->url_parent, array('id' => $child->getParent()->getID())));
    }

    protected function update(Item $item, $data)
    {
        // TODO: check required fields
        $name = trim($data['name']);

        $item->setName($name);

        if (isset($data['status']))
            $item->setStatus($data['status']);

        if (isset($data['parent']))
            $item->setParent($data['parent']);
    }
    
    protected function findChild($item_id)
    {
        $child = $this->getDoctrine()
            ->getRepository($this->child_repo)
            ->find($item_id);

        // TODO: throw another kind of exception (404)
        if ($child == null)
            throw new AccessDeniedException();

        return $child;
    }

    protected function findParent($item_id)
    {
        // get parent
        $parent = $this->getDoctrine()
            ->getRepository($this->parent_repo)
            ->find($item_id);

        // TODO: throw another kind of exception (404)
        if ($parent == null)
            throw new AccessDeniedException();

        return $parent;
    }

    protected function fetchAll($item_id)
    {
        $user = $this->getUser();
        $parent = $this->findParent($item_id);

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
