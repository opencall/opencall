<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use OnCall\Bundle\AdminBundle\Model\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;
use Plivo\HangupCause;
use Plivo\DurationModifier;
use Plivo\Log\Filter as LogFilter;
use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Repository as LogRepo;
use Predis\Client as PredisClient;

class CallLogController extends Controller
{
    protected function getLogFilter()
    {
        // filter
        $get = $this->getRequest()->query;
        $log_filter = new LogFilter(
            $get->get('cid'),
            $get->get('agid'),
            $get->get('adid'),
            $get->get('hcause'),
            $get->get('dmod'),
            $get->get('dsec'),
            $get->get('num'),
            $get->get('failed'),
            $get->get('dts'),
            $get->get('dte')
        );

        return $log_filter;
    }

    protected function getOngoingCalls($client_id, $redis, $log_repo, $prefix = 'plivo:ongoing')
    {
        $ongoing = array();


        $keys = $redis->keys($prefix . '*');
        foreach ($keys as $key)
        {
            $serial_qmsg = $redis->get($key);
            $qmsg = unserialize($serial_qmsg);
            $log = LogEntry::createFromMessage($qmsg, false);

            // check if it's for client
            if ($log->getClientID() != $client_id)
                continue;

            $log_data = $log->getData();
            $names = $log_repo->fetchNames($log_data['advert_id']);
            $log_data['advert_name'] = $names['advert_name'];
            $log_data['adgroup_name'] = $names['adgroup_name'];
            $log_data['campaign_name'] = $names['campaign_name'];

            $ongoing[] = $log_data;
        }

        return $ongoing;
    }

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
        $log_filter = $this->getLogFilter();
        $logs = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:CallLog')
            ->findLatest($id, $log_filter);

        // get ongoing
        // TODO: find out how to set this for production server
        $rconf = array(
            'scheme' => $this->container->getParameter('redis_scheme'),
            'host' => $this->container->getParameter('redis_host'),
            'port' => $this->container->getParameter('redis_port')
        );
        $redis = new PredisClient($rconf);
        $conn = $this->get('database_connection');
        $log_repo = new LogRepo($conn->getWrappedConnection());
        $ongoing = $this->getOngoingCalls($id, $redis, $log_repo);

        return $this->render(
            'OnCallAdminBundle:CallLog:index.html.twig',
            array(
                'hangup_causes' => HangupCause::getAll(),
                'duration_mods' => DurationModifier::getAll(),
                'filter' => $log_filter,
                'filter_json' => json_encode($log_filter->toData()),
                'user' => $user,
                'client' => $this->getClient(),
                'campaigns' => $this->getClient()->getCampaigns(),
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'call_log', $this->getClientID()),
                'agg_filter' => $daily_filter,
                'daily' => $daily,
                'hourly' => $hourly,
                'logs' => $logs,
                'ongoing' => $ongoing
            )
        );
    }

    public function moreAction($client_id, $last_id)
    {
        // get logs
        $log_filter = $this->getLogFilter();
        $logs = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:CallLog')
            ->findLatest($client_id, $log_filter, $last_id);

        return $this->render(
            'OnCallAdminBundle:CallLog:more.html.twig',
            array(
                'logs' => $logs
            )
        );
    }
}
