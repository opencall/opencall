<?php

namespace Plivo;

use PDO;
use Plivo\Log\Repository as LogRepository;
use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Pusher as LogPusher;

class Callback extends Lockable
{
    protected $pdo;
    protected $zmq;

    public function __construct(PDO $pdo, $redis, $zmq)
    {
        parent::__construct($redis);
        $this->pdo = $pdo;
        $this->zmq = $zmq;
    }

    public function run($post)
    {
        $action = trim($post['DialAction']);

        // we only track hangup actions
        if ($action != 'hangup')
            return;

        $call_id = $post['CallUUID'];

        // lock
        $this->lock($call_id);

        // get log
        $log_repo = new LogRepository($this->pdo);
        $log = $log_repo->find($call_id);

        // update call log
        $b_status = $post['DialBLegStatus'];
        $b_hangup_cause = $post['DialBLegHangupCause'];
        $log_repo->updateCallback($call_id, $b_status, $b_hangup_cause);

        // send update to live log
        $log->setBStatus($b_status)
            ->setBHangupCause($b_hangup_cause);
        $log_pusher = new LogPusher($this->zmq);
        $log_pusher->send($log, 'callback');

        // TODO: aggregate adjust in case leg A was successful and leg B was not

        // unlock
        $this->unlock($call_id);
    }
}
