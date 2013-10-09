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

    public static function parse($post)
    {
        // parse from post parameters
        $params = new self();

        // TODO: figure out which ones are required and not
        $this->to = $_POST['To'];
        $this->from = $_POST['From'];
        $this->unique_id = $_POST['CallUUID'];
        $this->status = $_POST['CallStatus'];
        $this->direction = $_POST['Direction'];
        $this->forward_form = $_POST['ForwardedFrom'];
        $this->bill_rate = $_POST['BillRate'];
        $this->event = $_POST['Event'];
        $this->hangup_id = $_POST['ScheduledHangupId'];
        $this->hangup_cause = $_POST['HangupCause'];
        $this->leg_unique_id = $_POST['ALegUUID'];
        $this->leg_req_unique_id = $_POST['ALegRequestUUID'];

        return $params;
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
