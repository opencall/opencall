<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Predis\Client as PredisClient;
use Plivo\Parameters;
use Plivo\Response;
use Plivo\Router;
use Plivo\Action;

try
{
    // redis prefix
    $prefix = 'plivo:ongoing:';

    // setup redis
    $rconf = array(
        'scheme' => 'tcp',
        'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
        'port' => 6379
    );
    $redis = new PredisClient($rconf);
    // local redis
    $redis = new PredisClient();

    // setup mysql
    $dsn = 'mysql:host=db.oncall;dbname=oncall';
    $user = 'webuser';
    $pass = 'lks8jw23';
    $pdo_main = new PDO($dsn, $user, $pass);

    // TODO: fallback mysql setup

    // parse parameters
    $_POST = array(
        'To' => '4294967295',
        'From' => '203948',
        'CallUUID' => 'sd902349023'
    );
    $params = new Parameters($_POST);

    // get response based on params
    $router = new Router($pdo_main);
    $response = $router->resolve($params);

    // add as ongoing call to redis
    $key = $prefix . $params->getUniqueID();
    $this->redis->set($key, serialize($params));

    // output XML
    echo $response->renderXML();
}
catch (\Predis\Connection\ConnectionException $e)
{
    // catch redis error
    error_log('redis exception');
    $act_params = array(
        'language' => 'en-GB',
        'text' => 'There was a problem connecting your call. This error has been logged and we will rectify the problem as soon as possible.'
    );
    $response = new Response();
    $action = new Action(Action::TYPE_SPEAK, $act_params);
    $response->addAction($action);

    echo $response->renderXML();
}
catch (PDOException $e)
{
    // catch pdo / db error
    error_log('pdo exception');
    $act_params = array(
        'language' => 'en-GB',
        'text' => 'There was a problem connecting your call. This error has been logged and we will rectify the problem as soon as possible.'
    );
    $response = new Response();
    $action = new Action(Action::TYPE_SPEAK, $act_params);
    $response->addAction($action);

    echo $response->renderXML();
}
