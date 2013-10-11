<?php

namespace Plivo\Aggregate;

use DateTime;
use Plivo\Queue\Message;
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

    protected $failed = false;
    protected $plead = false;
    protected $duration = 0;

    public static function createFromMessasge(Message $msg)
    {
        $num_data = $msg->getNumberData();
        $hangup_data = $msg->getHangupParams();

        $entry = new self();

        // number data
        $entry->client_id = $num_data['client_id'];
        $entry->campaign_id = $num_data['campaign_id'];
        $entry->adgroup_id = $num_data['adgroup_id'];
        $entry->advert_id = $num_data['advert_id'];
        $entry->number_id = $num_data['number_id'];

        // hangup data

        // duration
        $entry->duration = $hangup_data->getDuration();

        // capture only the date + hour
        $date_start = new DateTime($hangup_data->getStartTime());
        $entry->date_in = $date_start->format('Y-m-d H') . ':00:00';

        // check if failed
        $status = new Status($hangup_data->getStatus());
        $entry->failed = $status->isFailed();

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
