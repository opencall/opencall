<?php

namespace Plivo;

class Parameters
{
    // params from plivo documentation
    protected $unique_id;
    protected $from;
    protected $to;
    protected $forward_from;
    protected $status;
    protected $direction;
    protected $leg_unique_id;
    protected $leg_req_unique_id;
    protected $hangup_cause;
    protected $duration;
    protected $bill_duration;

    // params from callback
    protected $b_hangup_cause;
    protected $b_status;

    // other params based on old answer / hangup scripts
    protected $bill_rate;
    protected $event;
    protected $hangup_id;
    protected $start_time;
    protected $answer_time;
    protected $end_time;

    public function __construct($post)
    {
        // TODO: figure out which ones are required and not

        // answer params
        $this->set('unique_id', $post, 'CallUUID')
            ->set('from', $post, 'From')
            ->set('to', $post, 'To')
            ->set('forward_from', $post, 'ForwardedFrom')
            ->set('status', $post, 'CallStatus')
            ->set('direction', $post, 'Direction')
            ->set('leg_unique_id', $post, 'ALegUUID')
            ->set('leg_req_unique_id', $post, 'ALegRequestUUID')
            ->set('hangup_cause', $post, 'HangupCause')
            ->set('duration', $post, 'Duration')
            ->set('bill_duration', $post, 'BillDuration')

            ->set('b_hangup_cause', $post, 'DialBLegHangupCause')
            ->set('b_status', $post, 'DialBLegStatus')

            ->set('bill_rate', $post, 'BillRate')
            ->set('event', $post, 'Event')
            ->set('hangup_id', $post, 'ScheduledHangupId')
            ->set('start_time', $post, 'StartTime')
            ->set('answer_time', $post, 'AnswerTime')
            ->set('end_time', $post, 'EndTime');
    }

    protected function set($prop, $var, $name)
    {
        // set property if variable exists
        if (isset($var[$name]))
            $this->$prop = $var[$name];
        else
            $this->$prop = '';

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

    public function getBillDuration()
    {
        return $this->bill_duration;
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
    
    public function getStartTime()
    {
        return $this->start_time;
    }

    public function getAnswerTime()
    {
        return $this->answer_time;
    }

    public function getEndTime()
    {
        return $this->end_time;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getBStatus()
    {
        return $this->b_status;
    }

    public function getBHangupCause()
    {
        return $this->b_hangup_cause;
    }
}
