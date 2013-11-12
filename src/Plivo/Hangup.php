<?php

namespace Plivo;

use Predis\Client as PredisClient;
use Predis\Connection\ConnectionException;
use Plivo\Queue\Message as QMessage;
use Plivo\Queue\Handler as QHandler;
use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Repository as LogRepository;
use Plivo\Aggregate\Entry as AggEntry;
use Plivo\Aggregate\Repository as AggRepository;
use Plivo\Log\Pusher as LogPusher;
use PDO;
use DateTime;
use Plivo\AccountCounter\Repository as ACRepo;
use Plivo\AccountCounter\Entry as ACEntry;

class Hangup
{
    protected $pdo;
    protected $redis;
    protected $prefix;
    protected $zmq;

    public function __construct(PDO $pdo, PredisClient $redis, $zmq, $prefix = 'plivo:ongoing')
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->zmq = $zmq;
    }

    public function run($post)
    {
        try
        {
            // parse parameters
            $params = new Parameters($post);

            // get ongoing call data from redis
            $key = $this->prefix . $params->getUniqueID();
            $raw_qmsg = $this->redis->get($key);
            if ($raw_qmsg == null)
            {
                // TODO: what to do when there's no matching answer param
                error_log('no previous answer parameters');
                return null;
            }
            $qmsg = unserialize($raw_qmsg);
            $qmsg->setHangupParams($params);

            // delete key
            $this->redis->del($key);

            // start log and aggregate

            // log 
            $log_repo = new LogRepository($this->pdo);
            $log = LogEntry::createFromMessage($qmsg);
            $log_repo->persist($log);

            // aggregate
            $agg_repo = new AggRepository($this->pdo);
            $agg = AggEntry::createFromMessage($qmsg);
            $agg_repo->persist($agg);

            // live log
            $log_pusher = new LogPusher($this->zmq);
            $log_pusher->send($log);

            // account counter
            $num_data = $qmsg->getNumberData();
            $ac_repo = new ACRepo($this->pdo);
            $ac_entry = new ACEntry(new DateTime(), $num_data['user_id']);
            $ac_entry->setCall(1);
            $ac_entry->setDuration($qmsg->getHangupParams()->getDuration());
            $ac_repo->append($ac_entry);


            // end log and aggregate
        }
        catch (ConnectionException $e)
        {
            // catch redis error
            error_log('redis exception');
        }
        catch (PDOException $e)
        {
            // catch pdo / db error
            error_log('pdo exception');
        }
    }
}
