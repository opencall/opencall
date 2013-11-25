<?php

namespace Plivo\Aggregate;

use DateTime;
use Plivo\Log\Entry as LogEntry;
use Plivo\Status;

class Entry
{
    const PLEAD_THRESHOLD           = 120;

    protected $date_in;
    protected $client_id;
    protected $campaign_id;
    protected $adgroup_id;
    protected $advert_id;
    protected $number_id;
    protected $caller_id;

    protected $failed = false;
    protected $plead = false;
    protected $duration = 0;

    public static function createFromLog(LogEntry $log)
    {
        $entry = new self();

        // number data
        $entry->client_id = $log->getClientID();
        $entry->campaign_id = $log->getCampaignID();
        $entry->adgroup_id = $log->getAdGroupID();
        $entry->advert_id = $log->getAdvertID();
        $entry->number_id = $log->getDialledNumber();
        $entry->caller_id = $log->getOriginNumber();

        // hangup data
        $entry->duration = $log->getDuration();

        // capture only the date + hour
        $date_start = $log->getDateStart();
        $entry->date_in = $date_start->format('Y-m-d H') . ':00:00';

        // check if failed
        $entry->failed = $log->isFailed();

        // check if plead
        if ($entry->duration > self::PLEAD_THRESHOLD)
            $entry->plead = true;
        else
            $entry->plead = false;

        return $entry;
    }

    public function getDateIn()
    {
        return $this->date_in;
    }

    public function getClientID()
    {
        return $this->client_id;
    }

    public function getCampaignID()
    {
        return $this->campaign_id;
    }

    public function getAdGroupID()
    {
        return $this->adgroup_id;
    }

    public function getAdvertID()
    {
        return $this->advert_id;
    }

    public function getNumberID()
    {
        return $this->number_id;
    }

    public function getCallerID()
    {
        return $this->caller_id;
    }

    public function isFailed()
    {
        return $this->failed;
    }

    public function isPLead()
    {
        return $this->plead;
    }

    public function getDuration()
    {
        return $this->duration;
    }
}
