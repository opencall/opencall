<?php

namespace Plivo\Alert;

use Plivo\Log\Entry as LogEntry;

class Entry
{
    protected $client_id;
    protected $enabled;
    protected $email;
    protected $campaign_id;
    protected $adgroup_id;
    protected $advert_id;

    public function __construct()
    {
    }

    // BEGIN setters
    public function setClientID($client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setCampaignID($cid)
    {
        $this->campaign_id = $cid;
        return $this;
    }

    public function setAdGroupID($adg_id)
    {
        $this->adgroup_id = $adg_id;
        return $this;
    }

    public function setAdvertID($ad_id)
    {
        $this->advert_id = $ad_id;
        return $this;
    }
    // END setters

    // BEGIN getters
    public function getClientID()
    {
        return $this->client_id;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getEmail()
    {
        return $this->email;
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
    // END getters

    public function isTriggered(LogEntry $log)
    {
        // check if alert is enabled
        if (!$this->isEnabled())
            return false;

        // check if log entry is failed
        if (!$log->isFailed())
            return false;

        // campaign
        if ($this->campaign_id == null || $this->campaign_id == 0)
            return true;

        if ($this->campaign_id != $log->getCampaignID())
            return false;

        // ad group
        if ($this->adgroup_id == null || $this->adgroup_id == 0)
            return true;

        if ($this->adgroup_id != $log->getAdGroupID())
            return false;

        // advert
        if ($this->advert_id == null || $this->advert_id == 0)
            return true;

        if ($this->advert_id != $log->getAdvertID())
            return false;

        return true;
    }
}
