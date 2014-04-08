<?php

namespace Plivo\Alert;

use Plivo\Log\Entry as LogEntry;
use PHPMailer;

class Sender
{
    protected $repo;
    protected $mail_config;

    public function __construct(Repository $repo, $mail_config)
    {
        $this->repo = $repo;
        $this->mail_config = $mail_config;
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

    protected function filterText($alert, $log, $text)
    {
        $date_start = $log->getDateStart()->format('H:i') . '(GMT +8) on ' . $log->getDateStart()->format('l jS \o\f F');

        $ftext = str_replace('[date_in]', $date_start, $text);
        $ftext = str_replace('[origin_number]', $log->getOriginFormatted(), $ftext);
        $ftext = str_replace('[dialled_number]', $log->getDialledFormatted(), $ftext);
        $ftext = str_replace('[reason]', $log->getBHangupCause(), $ftext);
        $ftext = str_replace('[lead_rescue_url]', $this->mail_config['base_url'] . '/client/' . $log->getClientID() . '/lead_rescue', $ftext);
        $ftext = str_replace('[advert]', $this->fetchItemName('Advert', $log->getAdvertID()), $ftext);
        $ftext = str_replace('[adgroup]', $this->fetchItemName('AdGroup', $log->getAdGroupID()), $ftext);
        $ftext = str_replace('[campaign]', $this->fetchItemName('Campaign', $log->getCampaignID()), $ftext);

        return $ftext;
    }

    protected function email(Entry $alert, LogEntry $log)
    {
        error_log('sending email - ' . $alert->getEmail());

        $m_template = file_get_contents(__DIR__ . '/../../../email/alert.txt');
        $message = $this->filterText($alert, $log, $m_template);

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $this->mail_config['smtp_host'];
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Port = $this->mail_config['smtp_port'];
        $mail->Username = $this->mail_config['smtp_user'];
        $mail->Password = $this->mail_config['smtp_pass'];

        $mail->From = $this->mail_config['mail_from_email'];
        $mail->FromName = $this->mail_config['mail_from_name'];
        $mail->addAddress($alert->getEmail());
        $mail->addREplyTo($this->mail_config['mail_reply_email'], $this->mail_config['mail_reply_name']);
        $mail->Subject = $this->filterText($alert, $log, 'Missed Call Alert: [origin_number] called your ad: [advert] in [campaign].');
        $mail->Body = $message;
        $mail->IsHTML(true);

        $res = $mail->send();
        if (!$res)
        {
            error_log('mail sending error - ' . $mail->ErrorInfo);
            return false;
        }

        error_log('email sent!');
        return true;
    }

    protected function fetchItemName($table, $id)
    {
        $pdo = $this->repo->getPDO();

        $sql = "select * from $table where id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if (!$stmt->execute())
            return 'Unknown';

        $row = $stmt->fetch();
        if (!$row)
            return 'Unknown';


        return $row['name'];
    }
}
