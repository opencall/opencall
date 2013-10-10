<?php

namespace Plivo\Queue;

use DateTime;
use Plivo\Parameters;

// The incoming / outgoing message we use to interface with Plivo
class Message
{
    protected $ans_params;
    protected $hangup_params;
    protected $num_data;

    protected $timestamp;

    public function __construct()
    {
        $this->ans_params = null;
        $this->hangup_params = null;
        $this->num_data = null;
        $this->timestamp = new DateTime();
    }

    // setters
    public function setAnswerParams(Parameters $params)
    {
        $this->ans_params = $params;
        return $this;
    }

    public function setHangupParams(Parameters $params)
    {
        $this->hangup_params = $params;
        return $this;
    }

    public function setNumberData($num_data)
    {
        $this->num_data = $num_data;
        return $this;
    }

    // getters
    public function getAnswerParams()
    {
        return $this->ans_params;
    }

    public function getHangupParams()
    {
        return $this->hangup_params;
    }

    public function getNumberData()
    {
        return $this->num_data;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
