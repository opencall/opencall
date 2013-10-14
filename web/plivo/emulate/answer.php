<?php

require_once(__DIR__ . '/../../../app/autoload.php');

use Predis\Client as PredisClient;
use Plivo\Queue\Message as QMessage;
use Plivo\Parameters;
use Plivo\Response;
use Plivo\Router;
use Plivo\Action;

try
{
    // redis prefix
    $prefix = 'plivo:ongoing:';

    $redis = new PredisClient();

    // setup mysql
    $dsn = 'mysql:host=db.oncall;dbname=oncall';
    $user = 'webuser';
    $pass = 'lks8jw23';
    $pdo_main = new PDO($dsn, $user, $pass);

    // emulated post
    $_POST = array(
        'CallUUID' => 'test-230948029348902',
        'From' => '0000000000',
        'To' => '85235009088',
        'CallStatus' => 'ringing',
        'Direction' => 'inbound',
        'BillRate' => '0.00400',
        'Event' => 'StartApp'
    );

    // parse parameters
    $params = new Parameters($_POST);

    // get response based on params
    $router = new Router($pdo_main);
    $response = $router->resolve($params);
    $num_data = $router->getNumberData();

    // store response xml
    $xml = $response->renderXML();

    // setup queue message
    $qmsg = new QMessage();
    $qmsg->setAnswerParams($params);
    $qmsg->setNumberData($num_data);
    $qmsg->setResponseXML($xml);
    $serial_qmsg = serialize($qmsg);

    // add as ongoing call to redis
    $key = $prefix . $params->getUniqueID();
    $redis->set($key, $serial_qmsg);

    // output XML
    echo $xml;
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
