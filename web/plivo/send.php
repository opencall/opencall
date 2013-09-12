<?php

require_once(__DIR__ . '/../../app/autoload.php');

use OnCall\Entity\QueueHandler,
    OnCall\Entity\QueueMessage,
    Predis\Client;

// dev server
// setup redis
$rconf = array(
    'scheme' => 'tcp',
    'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
    'port' => 6379
);
$redis = new Client($rconf);

/*
// local
$redis = new Client();
*/

// sample $_POST emulation
$data = array(
    'sample' => 'test',
    '1' => '1233409',
    'number' => 123,
    'float' => 213.00
);

$msg = new QueueMessage();
$msg->setParams($data);

$sender = new QueueHandler($redis, 'plivo_in');
$sender->send($msg);

echo "Message sent to queue";
