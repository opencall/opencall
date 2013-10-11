<?php

require_once(__DIR__ . '/../../../app/autoload.php');

use Predis\Client as PredisClient;
use Plivo\Queue\Handler as QHandler;
use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Repository as LogRepository;
use Plivo\Aggregate\Entry as AggEntry;
use Plivo\Aggregate\Repository as AggRepository;

try
{
    // setup redis
    $queue_id = 'plivo_log';
    $redis = new PredisClient();

    // setup mysql
    $dsn = 'mysql:host=db.oncall;dbname=oncall';
    $user = 'webuser';
    $pass = 'lks8jw23';
    $pdo_main = new PDO($dsn, $user, $pass);

    $qh = new QHandler($redis, $queue_id);

    // log repo
    $log_repo = new LogRepository($pdo_main);

    // aggregate repo
    $agg_repo = new AggRepository($pdo_main);

    while ($raw_data = $qh->recv())
    {
        $data = unserialize($raw_data);

        // debug queue message
        print_r($data);

        // log
        $log = LogEntry::createFromMessage($data);
        print_r($log);
        $log_repo->persist($log);

        // aggregate
        $agg = AggEntry::createFromMessage($data);
        print_r($agg);
        $agg_repo->persist($agg);
    }
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
