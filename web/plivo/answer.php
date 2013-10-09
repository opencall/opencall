<?php

require_once(__DIR__ . '/../../app/autoload.php');

use OnCall\Model\QueueHandler;
use OnCall\Model\QueueMessage;
use Predis\Client as PredisClietn;
use Plivo\Parameters;
use Plivo\Response;

// dev server
// setup redis
$rconf = array(
    'scheme' => 'tcp',
    'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
    'port' => 6379
);
$redis = new PredisClient($rconf);
/*
// local
$redis = new PredisClient();
*/

// setup mysql
$dsn = 'mysql:host=db.oncall;dbname=oncall';
$user = 'webuser';
$pass = 'lks8jw23';
$pdo_main = new PDO($dsn, $user, $pass);

// TODO: fallback mysql setup
$pdo_second = new PDO($dsn, $user, $pass);

// parse parameters
$params = Parameters::parse($_POST);

// TODO: get number and see if active

// TODO: construct response
$response = new Response();

// send to redis queue
$msg = new QueueMessage();
$msg->setParams($data);

$sender = new QueueHandler($redis, 'plivo_in');
$sender->send($msg);


// output XML
echo $response->renderXML();
