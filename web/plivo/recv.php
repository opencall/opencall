<?php

require_once(__DIR__ . '/../../app/autoload.php');

use OnCall\Entity\QueueHandler,
    Predis\Client;

// redis setup
// setup redis
$rconf = array(
    'scheme' => 'tcp',
    'host' => 'localhost',
    'port' => 6379
);
$redis = new Client($rconf);

/*
// local
$redis = new Client();
*/

// queue handler
$qh = new QueueHandler($redis, 'plivo_in');


// grab a single one
$data = $qh->recv();
if ($data == null)
{
    echo "No data in queue available";
}
else
    print_r($data);

