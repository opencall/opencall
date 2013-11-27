<?php

namespace LiveLog;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Plivo\Log\Repository as LogRepo;

class Pusher implements WampServerInterface
{
    protected $subtopics = array();
    protected $log_repo;

    public function __construct(LogRepo $log_repo)
    {
        $this->log_repo = $log_repo;
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        if (!array_key_exists($topic->getId(), $this->subtopics))
        {
            $this->subtopics[$topic->getId()] = $topic;
        }
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

    public function onLogEntry($entry)
    {
        // echo "logging!\n";
        $data = json_decode($entry, true);

        if (!array_key_exists($data['topic'], $this->subtopics))
        {
            // echo "no subscription";
            return;
        }

        // fetch names from db
        $names = $this->log_repo->fetchNames($data['logentry']['advert_id']);
        print_r($names);
        $data['logentry']['advert_name'] = $names['advert_name'];
        $data['logentry']['adgroup_name'] = $names['adgroup_name'];
        $data['logentry']['campaign_name'] = $names['campaign_name'];

        $topic = $this->subtopics[$data['topic']];
        $topic->broadcast($data);
    }
}
