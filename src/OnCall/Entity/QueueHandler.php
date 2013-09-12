<?php

namespace OnCall\Entity;

use Predis\Client;

class QueueHandler
{
    protected $redis;
    protected $list_id;

    public function __construct(Client $redis, $list_id)
    {
        $this->redis = $redis;
        $this->list_id = $list_id;
    }

    protected function push(QueueMessage $data)
    {
        $serial = serialize($data);
        $ret = $this->redis->rpush($this->list_id, $serial);
        error_log($ret);

        return $this;
    }

    protected function pop()
    {
        return $this->redis->lpop($this->list_id);
    }

    public function send(QueueMessage $data)
    {
        return $this->push($data);
    }

    public function recv()
    {
        return $this->pop();
    }
}
