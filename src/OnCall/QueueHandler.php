<?php

namespace OnCall;

use FuseSource\Stomp\Stomp,
    FuseSource\Stomp\Exception\StompException;

class QueueHandler
{
    protected $user;
    protected $pass;
    protected $broker;
    protected $conn;
    protected $queue_id;

    public function __construct($broker)
    {
        $this->broker = $broker;
        $this->user = 'guest';
        $this->pass = 'guest';
        $this->conn = null;
        $this->queue_id = '/queue/plivoin';
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    public function connect()
    {
        try
        {
            $this->conn = new Stomp('tcp://localhost:61613');
            $this->conn->connect($this->user, $this->pass);
            return $this;
        }
        catch (StompException $e)
        {
            throw new Exception('Could not connect to Stomp Broker');
        }
    }

    public function disconnect()
    {
        if ($this->conn != null)
            $this->conn->disconnect();

        return $this;
    }

    public function send($data)
    {
        try
        {
            $serial = serialize($data);
            $this->conn->send($this->queue_id, $serial);
            return $this;
        }
        catch (StompException $e)
        {
            throw new Exception('Could not send data to Stomp Broker');
        }
    }

    public function recv()
    {
        try
        {
            echo "subscribe\n";
            $this->conn->subscribe($this->queue_id);
            echo "readFrame\n";
            $frame = $this->conn->readFrame();
            if ($frame == null)
                return null;
            echo "ack\n";
            if (!$this->conn->ack($frame))
                var_dump($this->conn->error());

            $data = unserialize($frame->body);
            if ($data === false)
                throw new Exception('Could not unserialize data from Stomp Broker');

            $this->disconnect();

            return $data;
        }
        catch (StompException $e)
        {
            print_r($e);
            throw new Exception('Could not recieve data from Stomp Broker');
        }
    }

    public function recvAll()
    {
        $this->conn->subscribe($this->queue_id);
        while (true)
        {
            $frame = $this->conn->readFrame();
            if ($frame != null)
            {
                $data = unserialize($frame->body);
                print_r($data);
                $this->conn->ack($frame);
            }
        }
    }
}
