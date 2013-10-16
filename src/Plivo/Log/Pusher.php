<?php

namespace Plivo\Log;

class Pusher
{
    protected $socket;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    public function send(Entry $log)
    {
        // form the data;
        $log_data = $log->getData();
        $data = array(
            'topic' => 'client:' . $log->getClientID(),
            'logentry' => $log_data
        );

        $json = json_encode($data);
        error_log($json);
        $this->socket->send($json);
        error_log('client:' . $log->getClientID());

        return $this;
    }


}
