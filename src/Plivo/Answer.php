<?php

namespace Plivo;

use Predis\Client as PredisClient;
use Predis\Connection\ConnectionException;
use Plivo\Queue\Message as QMessage;
use Plivo\Log\Entry as LogEntry;
use Plivo\Log\Pusher as LogPusher;
use PDO;

class Answer
{
    protected $prefix;
    protected $pdo;
    protected $redis;
    protected $zmq;
    protected $callback_url;

    public function __construct(PDO $pdo, PredisClient $redis, $zmq, $callback_url, $prefix = 'plivo:ongoing')
    {
        $this->pdo = $pdo;
        $this->prefix = $prefix;
        $this->redis = $redis;
        $this->zmq = $zmq;
        $this->callback_url = $callback_url;
    }

    public function run($post)
    {
        try
        {
            // parse parameters
            $params = new Parameters($post);

            // get response based on params
            $router = new Router($this->pdo);
            $router->setCallbackURL($this->callback_url);
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
            $key = $this->prefix . $params->getUniqueID();
            $this->redis->set($key, $serial_qmsg);

            // live log
            $log = LogEntry::createFromMessage($qmsg, false);
            $log_pusher = new LogPusher($this->zmq);
            $log_pusher->send($log);

            // output XML
            return $xml;
        }
        catch (ConnectionException $e)
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

            return $response->renderXML();
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

            return $response->renderXML();
        }
    }
}
