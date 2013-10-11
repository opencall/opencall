<?php

namespace OnCall\Bundle\AdminBundle\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

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

    protected function getFilter($type, $pid)
    {
        $filter = new AggregateFilter($type, $pid);

        // check dates
        $query = $this->getRequest()->query;
        $date_from = $query->get('date_from');
        $date_to = $query->get('date_to');
        if ($date_from != null)
            $filter->setDateFrom(new DateTime($date_from));
        if ($date_to != null)
            $filter->setDateTo(new DateTime($date_to));

        return $filter;
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
