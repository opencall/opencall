<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Predis\Client as PredisClient;
use Plivo\Queue\Handler as QHandler;

try
{
    // setup redis
    $queue_id = 'plivo_log';
    $rconf = array(
        'scheme' => 'tcp',
        'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
        'port' => 6379
    );
    $redis = new PredisClient($rconf);
    /*
    // NOTE: local redis
    $redis = new PredisClient();
    */

    // setup mysql
    $dsn = 'mysql:host=db.oncall;dbname=oncall';
    $user = 'webuser';
    $pass = 'lks8jw23';
    $pdo_main = new PDO($dsn, $user, $pass);

    $qh = new QHandler($redis, $queue_id);

    while ($raw_data = $qh->recv())
    {
        $data = unserialize($raw_data);

        echo "-------------------------------\n";
        print_r($data);
        echo "-------------------------------\n";
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
