<?php

namespace Plivo\Alert;

use Plivo\Log\Entry as LogEntry;

class Sender
{
    protected $repo;

    public function __construct(Repository $repo)
    {
        $this->repo = $repo;
    }

    public function send(LogEntry $log)
    {
        error_log('checking alert');
        // check if failed
        if (!$log->isFailed())
            return false;

        // get client alert info
        $alert = $this->repo->find($log->getClientID());
        if ($alert == null)
            return false;

        // check if alert is triggered
        if (!$alert->isTriggered($log))
            return false;

        // send email
        return $this->email($alert, $log);
    }

    protected function email(Entry $alert, LogEntry $log)
    {
        error_log('sending email - ' . $alert->getEmail());
        $subject = 'Calltracking.hk Lead Rescue Alert';
        $message = 'Failure of call.';
        mail($alert->getEmail(), $subject, $message);
    }
}
