<?php

require_once(__DIR__ . '/../app/autoload.php');

use Predis\Client as PredisClient;

// setup redis
$rconf = array(
    'scheme' => 'tcp',
    'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
    'port' => 6379
);
$redis = new PredisClient($rconf);


$keys = $redis->keys('plivo:ongoing*');
foreach ($keys as $key)
{
    echo "deleting $key\n";
    $redis->del($key);
}
