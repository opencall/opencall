<?php

namespace Plivo\Queue;

use DateTime;
use Plivo\Parameters;

// The incoming / outgoing message we use to interface with Plivo
class Message
{
    protected $params;
    protected $timestamp;

    public function __construct()
    {
        $this->params = null;
        $this->timestamp = new DateTime();
    }

    public function setParameters(Parameters $params)
    {
        $this->params = $params;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
