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
        $subject = 'Missed Call Alert: [origin_number] called your ad: [advert] in [campaign].';
        $message = file_get_contents(__DIR__ . '/../../../email/alert.txt');
        $headers = "From: noreply@calltracking.hk\r\n" .
            "Reply-To: noreply@calltracking.hk\r\n" .
            "X-Mailer: PHP/" . phpversion();

        mail($alert->getEmail(), $subject, $message, $headers);
    }
}
