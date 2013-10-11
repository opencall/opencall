<?php

require_once(__DIR__ . '/../../../app/autoload.php');

use Predis\Client as PredisClient;
use Plivo\Queue\Message as QMessage;
use Plivo\Queue\Handler as QHandler;
use Plivo\Parameters;
use Plivo\Response;
use Plivo\Router;
use Plivo\Action;

try
{
    // redis prefix
    $prefix = 'plivo:ongoing:';
    $queue_id = 'plivo_log';

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
        'To' => '85235009085',
        'CallStatus' => 'cancel',
        'Direction' => 'inbound',
        'HangupCause' => 'ORIGINATOR_CANCEL',
        'Duration' => 0,
        'BillDuration' => 0,
        'BillRate' => '0.00400',
        'Event' => 'Hangup',
        'StartTime' => '2013-10-08 09:28:00',
        'EndTime' => '2013-10-08 09:28:45',
    );

    // parse parameters
    $params = new Parameters($_POST);

    // get ongoing call data from redis
    $key = $prefix . $params->getUniqueID();
    $raw_qmsg = $redis->get($key);
    if ($raw_qmsg == null)
    {
        // TODO: what to do when there's no matching answer param
        error_log('no previous answer parameters');
        exit();
    }
    $qmsg = unserialize($raw_qmsg);
    $qmsg->setHangupParams($params);

    // delete key
    $redis->del($key);

    // enqueue for logging and aggregates
    $qh = new QHandler($redis, $queue_id);
    $qh->send($qmsg);
}
catch (\Predis\Connection\ConnectionException $e)
{
    // catch redis error
    error_log('redis exception');
}
catch (PDOException $e)
{
    // catch pdo / db error
    error_log('pdo exception');
}

echo '';
