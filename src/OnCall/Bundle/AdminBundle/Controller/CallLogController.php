<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use OnCall\Bundle\AdminBundle\Model\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;
use Plivo\HangupCause;
use Plivo\Log\Filter as LogFilter;

class CallLogController extends Controller
{
    public function indexAction($id)
    {
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();

        // get chart data (aggregates)
        $daily_filter = $this->getFilter(AggregateFilter::TYPE_DAILY_CLIENT, $id);
        $hourly_filter = $this->getFilter(AggregateFilter::TYPE_HOURLY_CLIENT, $id);
        $count_repo = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:Counter');

        $agg_daily = $count_repo->findChartAggregate($daily_filter);
        $agg_hourly = $count_repo->findChartAggregate($hourly_filter);

        $daily = $this->separateChartData($agg_daily);
        $hourly = $this->separateChartData($agg_hourly);

        // get logs
        $filter = array(
            'client_id' => $id
        );
        $logs = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:CallLog')
            ->findLatest($filter);

        // get request
        $get = $this->getRequest()->query;

        // filter
        $log_filter = new LogFilter(
            $get->get('cid'),
            $get->get('agid'),
            $get->get('adid'),
            $get->get('hcause')
        );

        return $this->render(
            'OnCallAdminBundle:CallLog:index.html.twig',
            array(
                'hangup_causes' => HangupCause::getAll(),
                'filter' => $log_filter,
                'user' => $user,
                'client' => $this->getClient(),
                'campaigns' => $this->getClient()->getCampaigns(),
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'call_log', $this->getClientID()),
                'agg_filter' => $daily_filter,
                'daily' => $daily,
                'hourly' => $hourly,
                'logs' => $logs
            )
        );
    }
}
