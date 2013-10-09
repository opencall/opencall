<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Plivo\Queue\Handler as QueueHandler;
use Plivo\Queue\Message as QueueMessage;
use Predis\Client as PredisClient;
use Plivo\Parameters;
use Plivo\Response;
use Plivo\Router;

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

// parse parameters
/*
$post = array(
    'To' => '4294967295'
);
*/
$params = Parameters::parse($_POST);
$params = new Parameters($post);

// get response based on params
$router = new Router($pdo_main);
$response = $router->resolve($params);

// send to redis queue
$msg = new QueueMessage();
$msg->setParams($data);

$sender = new QueueHandler($redis, 'plivo_in');
$sender->send($msg);

// output XML
echo $response->renderXML();
