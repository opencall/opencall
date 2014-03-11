<?php

namespace Plivo;

use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Pusher as LogPusher;
use Plivo\Log\Repository as LogRepository;
use PDO;
use OnCall\Bundle\AdminBundle\Model\Timezone;

class Answer
{
    protected $pdo;
    protected $zmq;
    protected $callback_url;
    protected $record_url;

    public function __construct(PDO $pdo, $zmq, $callback_url, $record_url)
    {
        $this->pdo = $pdo;
        $this->zmq = $zmq;
        $this->callback_url = $callback_url;
        $this->record_url = $record_url;
    }

    protected function createLog($num_data, $params)
    {
        $tzone = $num_data['timezone'];
        $cl_tzone = Timezone::toPHPTimezone($tzone);

        $log = new LogEntry();
        $date = $log->getDateIn();
        $date->setTimezone($cl_tzone);
        $log->setClientID($num_data['client_id'])
            ->setCampaignID($num_data['campaign_id'])
            ->setAdGroupID($num_data['adgroup_id'])
            ->setAdvertID($num_data['advert_id'])
            ->setDestinationNumber($num_data['destination'])
            ->setCallID($params->getUniqueID())
            ->setOriginNumber($params->getFrom())
            ->setDialledNumber($params->getTo())
            ->setDuration(0)
            ->setBillDuration(0)
            ->setBillRate($params->getBillRate())
            ->setStatus($params->getStatus())
            ->setHangupCause($params->getHangupCause())
            ->setDateStart($date)
            ->setDateEnd($date);


        return $log;
    }

    public function run($post)
    {
        try
        {
            // parse parameters
            $params = new Parameters($post);

            // get response based on params
            $router = new Router($this->pdo);
            $router->setCallbackURL($this->callback_url);
            $router->setRecordURL($this->record_url);
            $response = $router->resolve($params);
            $num_data = $router->getNumberData();

            // store response xml
            $xml = $response->renderXML();

            // save log
            $log = $this->createLog($num_data, $params);
            $log_repo = new LogRepository($this->pdo);
            $log_repo->insert($log);

            // live log
            $log_pusher = new LogPusher($this->zmq);
            $log_pusher->send($log);

            // output XML
            return $xml;
        }
        catch (PDOException $e)
        {
            // catch pdo / db error
            error_log('pdo exception');
            $act_params = array(
                'language' => 'en-GB',
                'text' => 'There was a problem connecting your call. This error has been logged and we will rectify the problem as soon as possible.'
            );
            $response = new Response();
            $action = new Action(Action::TYPE_SPEAK, $act_params);
            $response->addAction($action);

            return $response->renderXML();
        }
    }
}
