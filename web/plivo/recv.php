<?php

require_once(__DIR__ . '/../../app/autoload.php');

use OnCall\QueueHandler,
    Predis\Client;

// redis setup
// setup redis
$rconf = array(
    'scheme' => 'tcp',
    'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
    'port' => 6379
);
$redis = new Client($rconf);

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

