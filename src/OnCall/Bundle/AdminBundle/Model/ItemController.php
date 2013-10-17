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

    protected function checkParent($id)
    {
        // retrieve
        $parent = $this->findParent($id);

        // not found
        if ($parent == null)
            throw $this->createNotFoundException('Item does not exist.');

        // admin
        if ($this->get('security.context')->isGranted('ROLE_PREVIOUS_ADMIN'))
            return $parent;

        // inactive
        if ($parent->isInactive())
            throw $this->createNotFoundException('Item does not exist.');

        // we're ok
        return $parent;
    }

    protected function checkChild($id)
    {
        // retrieve
        $child = $this->findChild($id);

        // not found
        if ($child == null)
            throw $this->createNotFoundException('Item does not exist.');

        // check admin
        if ($this->get('security.context')->isGranted('ROLE_PREVIOUS_ADMIN'))
            return $child;

        // inactive
        if ($child->isInactive())
            throw $this->createNotFoundException('Item does not exist.');

        // we're ok
        return $child;
    }

    public function indexAction($id)
    {
        return $this->render($this->template, $this->fetchMainData($id));
    }

    public function createAction($id)
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();

        // find
        $parent = $this->checkParent($id);

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
        $child = $this->checkChild($id);

        return new Response($child->jsonify());
    }

    public function updateAction($id)
    {
        // find
        $child = $this->checkChild($id);

        // update
        $data = $this->getRequest()->request->all();
        $this->update($child, $data);
        $this->getDoctrine()
            ->getManager()
            ->flush();

        // success
        $this->addFlash('success', $this->name . ' ' . $child->getName() . ' has been updated.');

        return $this->redirect($this->generateUrl($this->url_parent, array('id' => $child->getParent()->getID())));
    }

    public function deleteAction($id)
    {
        // find and set inactive
        $child = $this->checkChild($id);

        $child->setInactive();
        $this->getDoctrine()
            ->getManager()
            ->flush();

        // success
        $this->addFlash('success', $this->name . ' ' . $child->getName() . ' has been deleted.');

        return new Response('');
    }

    // utility methods
    protected function fetchMainData($id)
    {
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();
        $client_id = $this->getClientID();

        // fetch needed data
        $fetch_res = $this->fetchAll($id);

        // aggregates
        $agg = $this->processAggregates($id, $fetch_res['child_ids']);

        return array(
            'user' => $user,
            'sidebar_menu' => MenuHandler::getMenu($role_hash, 'campaigns', $client_id),
            'parent' => $fetch_res['parent'],
            'agg_parent' => $agg['parent'],
            'agg_table' => $agg['table'],
            'agg_filter' => $this->getFilter($this->agg_type['parent'], $id),
            'daily' => $agg['daily'],
            'hourly' => $agg['hourly'],
            'children' => $fetch_res['children'],
            'top_color' => $this->top_color,
            'name' => $this->name,
            'url_child' => $this->url_child,
            'client_id' => $this->getClientID(),
            'filters' => $agg['filters'],
        );
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

        return $child;
    }

    protected function findParent($item_id)
    {
        // get parent
        $parent = $this->getDoctrine()
            ->getRepository($this->parent_repo)
            ->find($item_id);

        return $parent;
    }

    protected function fetchAll($item_id)
    {
        $user = $this->getUser();
        $parent = $this->checkParent($item_id);

        // children
        $method = $this->child_fetch_method;
        $children_prefilter = $parent->$method();

        // filter out inactive if not admin
        if ($this->get('security.context')->isGranted('ROLE_PREVIOUS_ADMIN'))
            $children = $children_prefilter;
        else
        {
            $children = array();
            foreach ($children_prefilter as $child)
            {
                if ($child->isActive())
                    $children[] = $child;
            }
        }

        // get children ids
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
        $filter = $this->getFilter($this->agg_type['parent'], $pid);
        $tfilter = $this->getFilter($this->agg_type['table'], $pid);
        $dfilter = $this->getFilter($this->agg_type['daily'], $pid);
        $hfilter = $this->getFilter($this->agg_type['hourly'], $pid);

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
            'hourly' => $hourly,
            'filters' => array(
                'parent' => $filter,
                'table' => $tfilter,
                'daily' => $dfilter,
                'hourly' => $hfilter
            )
        );
    }
}
