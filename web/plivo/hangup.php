<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Predis\Client as PredisClient;
use Plivo\Queue\Message as QMessage;
use Plivo\Queue\Handler as QHandler;
use Plivo\Parameters;
use Plivo\Response;
use Plivo\Router;
use Plivo\Action;
use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Repository as LogRepository;
use Plivo\Aggregate\Entry as AggEntry;
use Plivo\Aggregate\Repository as AggRepository;


try
{
    // redis prefix
    $prefix = 'plivo:ongoing:';
    $queue_id = 'plivo_log';

    // setup redis
    $rconf = array(
        'scheme' => 'tcp',
        'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
        'port' => 6379
    );
    $redis = new PredisClient($rconf);

    // setup mysql
    $dsn = 'mysql:host=db.oncall;dbname=oncall';
    $user = 'webuser';
    $pass = 'lks8jw23';
    $pdo_main = new PDO($dsn, $user, $pass);

    // TODO: fallback mysql setup

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

    // start log and aggregate
    // NOTE: this version is live, no queueing

    // log repo
    $log_repo = new LogRepository($pdo_main);

    // aggregate repo
    $agg_repo = new AggRepository($pdo_main);

    // log
    $log = LogEntry::createFromMessage($qmsg);
    $log_repo->persist($log);

    // aggregate
    $agg = AggEntry::createFromMessage($qmsg);
    $agg_repo->persist($agg);

    // end log and aggregate
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
