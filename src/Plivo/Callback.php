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

    public function __construct(PDO $pdo, $zmq)
    {
        $this->pdo = $pdo;
        $this->zmq = $zmq;
    }

    public function run($post)
    {
        $action = trim($post['DialAction']);
        $call_id = $post['CallUUID'];
        $b_status = $post['DialBLegStatus'];
        $b_hangup_cause = $post['DialBLegHangupCause'];

        // we only track hangup actions
        if ($action != 'hangup')
            return;

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
}
