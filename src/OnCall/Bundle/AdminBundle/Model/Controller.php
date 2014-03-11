<?php

namespace OnCall\Bundle\AdminBundle\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;
use OnCall\Bundle\AdminBundle\Model\Timezone;
use DateTime;
use DateTimeZone;


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
        // no specified client id
        $client_id = $this->getClientID();
        if ($client_id == null)
            throw $this->createNotFoundException('Client not specified.');

        // find client
        $client =  $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Client')
            ->find($client_id);
        if ($client == null)
            throw $this->createNotFoundException('Client not found.');

        // admin
        if ($this->get('security.context')->isGranted('ROLE_PREVIOUS_ADMIN'))
            return $client;
            
        // inactive
        if ($client->isInactive())
            throw $this->createNotFoundException('Client not found.');

        return $client;
    }

    protected function getClientTimezone()
    {
        $client = $this->getClient();
        $timezone = Timezone::toPHPTimezone($client->getTimezone());

        return $timezone;
    }

    protected function getFilter($type, $pid)
    {
        $filter = new AggregateFilter($type, $pid);
        $def_timezone = new DateTimeZone('Asia/Hong_Kong');
        $cl_timezone = $this->getClientTimezone();
        $filter->setClientTimezone($cl_timezone);

        // check dates
        $query = $this->getRequest()->query;
        $date_from = $query->get('date_from');
        $date_to = $query->get('date_to');

        // modify according to client timezone
        if ($date_from != null)
        {
            $curr_date_from = DateTime::createFromFormat('Y-m-d H:i:s', $date_from . ' 00:00:00', $cl_timezone);
            $curr_date_from->setTimezone($def_timezone);
            $filter->setDateFrom($curr_date_from);
        }

        if ($date_to != null)
        {
            $curr_date_to = DateTime::createFromFormat('Y-m-d H:i:s', $date_to . ' 23:59:59', $cl_timezone);
            $curr_date_from->setTimezone($def_timezone);
            $filter->setDateTo($curr_date_to);
        }

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
