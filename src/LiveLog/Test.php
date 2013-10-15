<?php

namespace LiveLog;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Test implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = array();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection - ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $num_recv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n", 
            $from->resourceId,
            $msg,
            $num_recv,
            $num_recv == 1 ? '' : 's'
        );

        foreach ($this->clients as $client)
        {
            if ($from !== $client)
            {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occured: {$e->getMessage()}\n";

        $conn->close();
    }
}
