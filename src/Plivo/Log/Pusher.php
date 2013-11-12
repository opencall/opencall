<?php

namespace Plivo\Log;

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
        $this->socket->send($json);

        return $this;
    }


}
