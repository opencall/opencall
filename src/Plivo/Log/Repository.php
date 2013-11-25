<?php

namespace Plivo\Log;

use PDO;
use DateTime;

class Repository
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function createEntry($row)
    {
        $entry = new Entry();
        $entry->setCallID($row['call_id'])
            ->setOriginNumber($row['origin_number'])
            ->setDialledNumber($row['dialled_number'])
            ->setDestinationNumber($row['destination_number'])
            ->setDateStart(new DateTime($row['date_start']))
            ->setDateEnd(new DateTime($row['date_end']))
            ->setDuration($row['duration'])
            ->setBillDuration($row['bill_duration'])
            ->setBillRate($row['bill_rate'])
            ->setStatus($row['status'])
            ->setHangupCause($row['hangup_cause'])
            ->setAdvertID($row['advert_id'])
            ->setAdGroupID($row['adgroup_id'])
            ->setCampaignID($row['campaign_id'])
            ->setClientID($row['client_id'])
            ->setBStatus($row['b_status'])
            ->setBHangupCause($row['b_hangup_cause'])
            ->setAudioRecord($row['audio_record']);

        return $entry;
    }

    public function find($call_id)
    {
        $sql = 'select * from CallLog where call_id = :call_id limit 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':call_id', $call_id);

        if (!$stmt->execute())
            return null;

        $row = $stmt->fetch();
        if (!$row)
            return null;

        return $this->createEntry($row);
    }

    public function findClientID($call_id)
    {
        $sql = 'select client_id from CallLog where call_id = :call_id limit 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':call_id', $call_id);

        if (!$stmt->execute())
            return 0;

        $row = $stmt->fetch();
        if (!$row)
            return 0;

        return $row['client_id'];
    }

    public function updateHangup(Entry $log)
    {
        $sql = 'update CallLog set bill_rate = :bill_rate, bill_duration = :bill_duration, duration = :duration, date_start = :date_start, date_end = :date_end, status = :status, hangup_cause = :hangup_cause where call_id = :call_id';
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':bill_rate', $log->getBillRate());
        $stmt->bindParam(':bill_duration', $log->getBillDuration());
        $stmt->bindParam(':duration', $log->getDuration());
        $stmt->bindParam(':date_start', $log->getDateStart()->format('Y-m-d H:i:s'));
        $stmt->bindParam(':date_end', $log->getDateEnd()->format('Y-m-d H:i:s'));
        $stmt->bindParam(':status', $log->getStatus());
        $stmt->bindParam(':hangup_cause', $log->getHangupCause());
        $stmt->bindParam(':call_id', $log->getCallID());

        return $stmt->execute();
    }

    public function updateCallback($call_id, $b_status, $b_hangup_cause)
    {
        $sql = 'update CallLog set b_status = :b_status, b_hangup_cause = :b_hangup_cause where call_id = :call_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':b_status', $b_status);
        $stmt->bindParam(':b_hangup_cause', $b_hangup_cause);
        $stmt->bindParam(':call_id', $call_id);

        return $stmt->execute();
    }

    public function insert(Entry $log)
    {
        // insert log entry into database
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

    public function fetchNames($advert_id)
    {
        $sql = 'select Advert.name as advert_name, AdGroup.name as adgroup_name, Campaign.name as campaign_name from Advert, AdGroup, Campaign where Advert.id = :advert_id and Advert.adgroup_id = AdGroup.id and AdGroup.campaign_id = Campaign.id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':advert_id', $advert_id);

        // execute
        if (!$stmt->execute())
            return array(
                'advert_name' => '',
                'adgroup_name' => '',
                'campaign_name' => ''
            );

        // fetch row
        $row = $stmt->fetch();
        if (!$row)
            return array(
                'advert_name' => '',
                'adgroup_name' => '',
                'campaign_name' => ''
            );

        return array(
            'advert_name' => $row['advert_name'],
            'adgroup_name' => $row['adgroup_name'],
            'campaign_name' => $row['campaign_name'] 
        );
    }
}
