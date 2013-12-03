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
            )
        );
    }

    public function csvAction($client_id)
    {
        // fetch
        $log_filter = $this->getLogFilter();
        $logs = $this->getDoctrine()
            ->getRepository('OnCallAdminBundle:CallLog')
            ->findFiltered($client_id, $log_filter);

        // build csv data array
        $data = array();

        // header row
        $row = array(
            'Date',
            'Time',
            'Caller Number',
            'Dialled Number',
            'Destination Number',
            'Advert',
            'Ad Group',
            'Campaign',
            'Call Duration',
            'Billable Minutes',
            'Call Result',
            'Call Result A',
            'Call Result B',
            'Recording URL'
        );
        $data[] = $row;

        // data rows
        foreach ($logs as $log)
        {
            $succ_text = 'success';
            if ($log->isFailed())
                $succ_text = 'failed';

            $row = array(
                $log->getDateStart()->format('d-m-Y'),
                $log->getDateStart()->format('H:m:s'),
                $log->getOriginNumber(),
                $log->getDialledNumber(),
                $log->getDestinationNumber(),
                $log->getAdvert()->getName(),
                $log->getAdGroup()->getName(),
                $log->getCampaign()->getName(),
                $log->getDuration(),
                floor($log->getBillDuration() / 60),
                $succ_text,
                $log->getHangupCause(),
                $log->getHangupCauseB(),
                $log->getAudioRecord()
            );
            $data[] = $row;
        }

        // build response
        $resp = $this->render(
            'OnCallAdminBundle:CallLog:index.csv.twig',
            array(
                'csv_data' => $data
            )
        );

        // set http headers
        $resp->headers->set('Content-Type', 'text/csv');
        $resp->headers->set('Content-Description', 'Call Log');
        $resp->headers->set('Content-Disposition', 'attachment; filename=call_log.csv');
        $resp->headers->set('Content-Transfer-Encoding', 'binary');
        $resp->headers->set('Pragma', 'no-cache');
        $resp->headers->set('Expires', '0');

        return $resp;
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
