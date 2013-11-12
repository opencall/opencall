<?php

namespace Plivo\Queue;

use DateTime;
use Plivo\Parameters;

// The incoming / outgoing message we use to interface with Plivo
class Message
{
    protected $ans_params;
    protected $hangup_params;
    protected $callback_params;
    protected $num_data;
    protected $xml;

    protected $timestamp;

    public function __construct()
    {
        $this->ans_params = null;
        $this->hangup_params = null;
        $this->callback_params = null;
        $this->num_data = null;
        $this->xml = '';
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

    public function setCallbackParams(Parameters $params)
    {
        $this->callback_params = $params;
        return $this;
    }

    public function setNumberData($num_data)
    {
        $this->num_data = $num_data;
        return $this;
    }

    public function setResponseXML($xml)
    {
        $this->xml = $xml;
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

    public function getCallbackParams()
    {
        return $this->callback_params;
    }

    public function getNumberData()
    {
        return $this->num_data;
    }

    public function getResponseXML()
    {
        return $this->xml;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
