<?php

namespace Plivo;

class Parameters
{
    protected $to;
    protected $from;
    protected $unique_id;
    protected $status;
    protected $direction;
    protected $forward_from;
    protected $bill_rate;
    protected $event;
    protected $hangup_id;
    protected $hangup_cause;
    protected $leg_unique_id;
    protected $leg_req_unique_id;

    public function __construct($post)
    {
        // TODO: figure out which ones are required and not
        $this->set('to', $post, 'To')
            ->set('from', $post, 'From')
            ->set('unique_id', $post, 'CallUUID')
            ->set('status', $post, 'CallStatus')
            ->set('direction', $post, 'Direction')
            ->set('forward_from', $post, 'ForwardedFrom')
            ->set('bill_rate', $post, 'BillRate')
            ->set('event', $post, 'Event')
            ->set('hangup_id', $post, 'ScheduledHangupId')
            ->set('hangup_cause', $post, 'HangupCause')
            ->set('leg_unique_id', $post, 'ALegUUID')
            ->set('leg_req_unique_id', $post, 'ALegRequestUUID');
    }

    protected function set($prop, $var, $name)
    {
        // error_log("set - $prop - $name");
        // error_log(print_r($var, true));
        if (isset($var[$name]))
            $this->$prop = $var[$name];
        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getUniqueID()
    {
        return $this->unique_id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function getForwardFrom()
    {
        return $this->forward_from;
    }

    public function getBillRate()
    {
        return $this->bill_rate;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getHangupID()
    {
        return $this->hangup_id;
    }

    public function getHangupCause()
    {
        return $this->hangup_cause;
    }

    public function getLegID()
    {
        return $this->leg_unique_id;
    }

    public function getLegRequestID()
    {
        return $this->leg_req_unique_id;
    }
}
