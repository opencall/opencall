<?php

namespace Plivo;

use PDO;
use Plivo\Log\Repository as LogRepository;
use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Pusher as LogPusher;

class Callback
{
    protected $pdo;
    protected $zmq;
    protected $redis;
    protected $prefix;

    public function __construct(PDO $pdo, $redis, $zmq, $prefix = 'plivo:ongoing')
    {
        $this->pdo = $pdo;
        $this->zmq = $zmq;
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    public function run($post)
    {
        $action = trim($post['DialAction']);
        $call_id = $post['CallUUID'];

        // we only track hangup actions
        if ($action != 'hangup')
            return;

        // try redis
        $key = $this->prefix . $call_id;
        $raw_qmsg = $this->redis->get($key);
        if ($raw_qmsg == null)
        {
            $b_status = $post['DialBLegStatus'];
            $b_hangup_cause = $post['DialBLegHangupCause'];
            $this->postHangupProcess($call_id, $b_status, $b_hangup_cause);
        }
        else
        {
            $qmsg = unserialize($raw_qmsg);
            $this->preHangupProcess($key, $qmsg, $post);
        }
    }

    public function postHangupProcess($qmsg, $call_id, $b_status, $b_hangup_cause)
    {
        // update call log
        $log_repo = new LogRepository($this->pdo);
        $log_repo->updateCallback($call_id, $b_status, $b_hangup_cause);

        // get client id
        $client_id = $log_repo->findClientID($call_id);

        // create log entry to pass
        $entry = new LogEntry();
        $entry->setCallID($call_id)
            ->setBStatus($b_status)
            ->setBHangupCause($b_hangup_cause)
            ->setClientID($client_id);

        // send update to live log
        $log_pusher = new LogPusher($this->zmq);
        $log_pusher->send($entry, 'callback');
    }

    public function preHangupProcess($key, $qmsg, $post)
    {
        $params = new Parameters($post);
        $qmsg->setCallbackParams($params);
        $serial_qmsg = serialize($qmsg);
        $this->redis->set($key, $serial_qmsg);

        // don't push, hangup will do it for us
    }
}
