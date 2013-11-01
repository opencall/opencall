<?php

namespace Plivo\AccountCounter;

use DateTime;

class Entry
{
    protected $date_in;
    protected $user_id;
    protected $client;
    protected $number;
    protected $call;
    protected $duration;

    public function __construct(DateTime $date_in, $user_id)
    {
        $this->date_in = $date_in;
        $this->user_id = $user_id;
        $this->client = 0;
        $this->number = 0;
        $this->call = 0;
        $this->duration = 0;
    }

    public function setClient($count)
    {
        $this->client = $count;
        return $this;
    }

    public function setNumber($count)
    {
        $this->number = $count;
        return $this;
    }

    public function setCall($count)
    {
        $this->call = $count;
        return $this;
    }

    public function setDuration($count)
    {
        $this->duration = $count;
        return $this;
    }

    public function getDateIn()
    {
        return $this->date_in;
    }

    public function getUserID()
    {
        return $this->user_id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getCall()
    {
        return $this->call;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getDurationFormatted()
    {
        $seconds = $this->duration;

        $hours = floor($seconds / 3600);
        $remain = $seconds % 3600;
        $mins = floor($remain / 60);
        $secs = $remain % 60;

        return sprintf("%d:%02d:%02d", $hours, $mins, $secs);
    }
}
