<?php

namespace OnCall;

use DateTime;

// The incoming / outgoing message we use to interface with Plivo
class QueueMessage
{
    protected $params;
    protected $timestamp;

    public function __construct()
    {
        $this->params = array();
        $this->timestamp = new DateTime();
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    public function getParam($name)
    {
        return $this->params[$name];
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
