<?php

namespace Plivo\Log;

use PDO;

class Repository
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function persist(Entry $log)
    {
        // persist log entry into database

        $sql = 'insert into CallLog (date_in, call_id, origin_number, dialled_number, destination_number, date_start, date_end, duration, bill_duration, bill_rate, status, hangup_cause, advert_id, adgroup_id, campaign_id, client_id) values (now(), :call_id, :origin, :dialled, :destination, :date_start, :date_end, :duration, :bill_duration, :bill_rate, :status, :hangup_cause, :advert_id, :adgroup_id, :campaign_id, :client_id)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':call_id', $log->getCallID());
        $stmt->bindParam(':origin', $log->getOriginNumber());
        $stmt->bindParam(':dialled', $log->getDialledNumber());
        $stmt->bindParam(':destination', $log->getDestinationNumber());
        $stmt->bindParam(':date_start', $log->getDateStart()->format('Y-m-d H:i:s'));
        $stmt->bindParam(':date_end', $log->getDateEnd()->format('Y-m-d H:i:s'));
        $stmt->bindParam(':duration', $log->getDuration());
        $stmt->bindParam(':bill_duration', $log->getBillDuration());
        $stmt->bindParam(':bill_rate', $log->getBillRate());
        $stmt->bindParam(':status', $log->getStatus());
        $stmt->bindParam(':hangup_cause', $log->getHangupCause());
        $stmt->bindParam(':advert_id', $log->getAdvertID());
        $stmt->bindParam(':adgroup_id', $log->getAdGroupID());
        $stmt->bindParam(':campaign_id', $log->getCampaignID());
        $stmt->bindParam(':client_id', $log->getClientID());

        return $stmt->execute();
    }
}
