<?php

namespace Plivo\Queue;

use Predis\Client;

class Handler
{
    protected $redis;
    protected $list_id;

    public function __construct(Client $redis, $list_id)
    {
        $this->redis = $redis;
        $this->list_id = $list_id;
    }

    protected function push(Message $data)
    {
        $serial = serialize($data);
        $ret = $this->redis->rpush($this->list_id, $serial);

        return $this;
    }

    protected function pop()
    {
        return $this->redis->lpop($this->list_id);
    }

    public function send(Message $data)
    {
        return $this->push($data);
    }

    public function recv()
    {
        return $this->pop();
    }
}
