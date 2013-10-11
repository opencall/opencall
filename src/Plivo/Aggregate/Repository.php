<?php

namespace Plivo\Aggregate;

use PDO;

class Repository
{
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function persist(Entry $entry)
    {
        $sql = 'insert into Counter (date_in, client_id, campaign_id, adgroup_id, advert_id, number_id, caller_id, count_total, count_plead, count_failed, duration_secs) values (:date_in, :client_id, :campaign_id, :adgroup_id, :advert_id, :number_id, :caller_id, 1, :count_plead, :count_failed, :duration)';
        $sql .= ' on duplicate key update count_total = count_total + 1, count_plead = count_plead + :count_plead, count_failed = count_failed + :count_failed, duration_secs = duration_secs + :duration';

        $count_failed = 0;
        $count_plead = 0;

        if ($entry->isFailed())
            $count_failed = 1;

        if ($entry->isPLead())
            $count_plead = 1;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':date_in', $entry->getDateIn());
        $stmt->bindParam(':client_id', $entry->getClientID());
        $stmt->bindParam(':campaign_id', $entry->getCampaignID());
        $stmt->bindParam(':adgroup_id', $entry->getAdGroupID());
        $stmt->bindParam(':advert_id', $entry->getAdvertID());
        $stmt->bindParam(':number_id', $entry->getNumberID());
        $stmt->bindParam(':caller_id', $entry->getCallerID());
        $stmt->bindParam(':count_failed', $count_failed);
        $stmt->bindParam(':count_plead', $count_plead);
        $stmt->bindParam(':duration', $entry->getDuration());

        return $stmt->execute();
    }
}
