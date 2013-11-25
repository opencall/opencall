<?php

namespace Plivo;

class Lockable
{
    protected $redis;
    protected $prefix;

    public function __construct($redis, $prefix = 'log_lock')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->lock_timeout = 100;
    }

    protected function getRedisKey($call_id)
    {
        return $this->prefix . ':' . $call_id;
    }

    protected function lock($call_id)
    {
        $key = $this->getRedisKey($call_id);

        // loop till we acquire lock
        while ($this->redis->set($key, $call_id, 'EX', $this->lock_timeout, 'NX') == null)
        {
            sleep(1);
        }

        return true;
    }

    protected function unlock($call_id)
    {
        $this->redis->del($this->getRedisKey($call_id));
        return true;
    }
}
