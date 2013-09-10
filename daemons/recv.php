<?php

require_once(__DIR__ . '/../app/autoload.php');

use OnCall\QueueHandler,
    Predis\Client;

// redis setup
$redis = new Client();

// queue handler
$qh = new QueueHandler($redis, 'plivo_in');

while (true)
{
    $data = $qh->recv();
    if ($data == null)
    {
        sleep(1);
        continue;
    }

    echo "\n-----------------------------------\n";
    print_r($data);
    echo "\n-----------------------------------\n";
}

