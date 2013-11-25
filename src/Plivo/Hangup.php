<?php

namespace Plivo;

use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Repository as LogRepository;
use Plivo\Aggregate\Entry as AggEntry;
use Plivo\Aggregate\Repository as AggRepository;
use Plivo\Log\Pusher as LogPusher;
use PDO;
use DateTime;
use Plivo\AccountCounter\Repository as ACRepo;
use Plivo\AccountCounter\Entry as ACEntry;

class Hangup extends Lockable
{
    protected $pdo;
    protected $zmq;

    public function __construct(PDO $pdo, $redis, $zmq)
    {
        parent::__construct($redis);
        $this->pdo = $pdo;
        $this->zmq = $zmq;
    }

    protected function updateLog(LogEntry $log, $params)
    {
        $log->setBillRate($params->getBillRate())
            ->setBillDuration($params->getBillDuration())
            ->setDuration($params->getDuration())
            ->setDateStart(new DateTime($params->getStartTime()))
            ->setDateEnd(new DateTime($params->getEndTime()))
            ->setStatus($params->getStatus())
            ->setHangupCause($params->getHangupCause());

        return $log;
    }

    public function run($post)
    {
        try
        {
            // parse parameters
            $params = new Parameters($post);

            // lock call_id
            $this->lock($params->getUniqueID());

            // start log and aggregate

            // get log
            $log_repo = new LogRepository($this->pdo);
            $log = $log_repo->find($params->getUniqueID());

            // update log with hangup data
            $this->updateLog($log, $params);
            $log_repo->updateHangup($log);

            // aggregate
            $agg_repo = new AggRepository($this->pdo);
            $agg = AggEntry::createFromLog($log);
            $agg_repo->persist($agg);

            // live log
            $log_pusher = new LogPusher($this->zmq);
            $log_pusher->send($log);

            // TODO: account counter
            /*
            $num_data = $qmsg->getNumberData();
            $ac_repo = new ACRepo($this->pdo);
            $ac_entry = new ACEntry(new DateTime(), $num_data['user_id']);
            $ac_entry->setCall(1);
            $ac_entry->setDuration($qmsg->getHangupParams()->getDuration());
            $ac_repo->append($ac_entry);
            */

            // unlock call_id
            $this->unlock($params->getUniqueID());

            // end log and aggregate
        }
        catch (PDOException $e)
        {
            // catch pdo / db error
            error_log('pdo exception');
        }
    }
}
