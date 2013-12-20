<?php

namespace Plivo\Log;

use ZMQ;

class Pusher
{
    protected $socket;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    public function send(Entry $log, $type = 'log')
    {
        // form the data;
        $log_data = $log->getData();
        $data = array(
            'topic' => 'client:' . $log->getClientID(),
            'logentry' => $log_data,
            'type' => $type
        );

        $json = json_encode($data);
        error_log('sending log');
        $this->socket->send($json, ZMQ::MODE_NOBLOCK);

        return $this;
    }


}
