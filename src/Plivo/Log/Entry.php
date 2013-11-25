<?php

namespace Plivo\Log;

use DateTime;
use DateTimeZone;
use Plivo\Queue\Message;
use Plivo\NumberFormatter;

class Entry
{
    protected $id;
    protected $date_in;
    protected $call_id;
    protected $origin_number;
    protected $dialled_number;
    protected $destination_number;
    protected $date_start;
    protected $date_end;
    protected $duration;
    protected $bill_duration;
    protected $bill_rate;
    protected $status;
    protected $hangup_cause;
    protected $advert_id;
    protected $adgroup_id;
    protected $campaign_id;
    protected $client_id;
    protected $response_xml;
    protected $b_status;
    protected $b_hangup_cause;
    protected $audio_record;

    public function __construct()
    {
        $this->date_in = new DateTime('now', new DateTimeZone('Asia/Hong_Kong'));
        $this->b_hangup_cause = '';
        $this->b_status = '';
        $this->audio_record = null;
    }

    public static function createFromMessage(Message $msg, $use_hangup = true)
    {
        $num_data = $msg->getNumberData();

        if ($use_hangup)
            $data = $msg->getHangupParams();
        else
            $data = $msg->getAnswerParams();

        $entry = new self();

        // number data
        $entry->setClientID($num_data['client_id'])
            ->setCampaignID($num_data['campaign_id'])
            ->setAdGroupID($num_data['adgroup_id'])
            ->setAdvertID($num_data['advert_id'])
            ->setDestinationNumber($num_data['destination']);

        // data
        $entry->setCallID($data->getUniqueID())
            ->setOriginNumber($data->getFrom())
            ->setDialledNumber($data->getTo())
            ->setDuration($data->getDuration())
            ->setBillDuration($data->getBillDuration())
            ->setBillRate($data->getBillRate())
            ->setStatus($data->getStatus())
            ->setHangupCause($data->getHangupCause());

        // callback data
        $cb_data = $msg->getCallbackParams();
        if ($cb_data != null)
        {
            $entry->setBStatus($cb_data->getBStatus())
                ->setBHangupCause($cb_data->getBHangupCause());
        }

        if ($use_hangup)
        {
            $entry->setDateStart(new DateTime($data->getStartTime()))
                ->setDateEnd(new DateTime($data->getEndTime()));
        }
        else
        {
            // use date in as date start if answer
            $entry->setDateStart($entry->getDateIn())
                ->setDuration(0)
                ->setHangupCause('');
        }


        // response xml
        $entry->setResponseXML($msg->getResponseXML());

        return $entry;
    }

    // setters
    public function setCallID($call_id)
    {
        $this->call_id = $call_id;
        return $this;
    }

    public function setOriginNumber($num)
    {
        $this->origin_number = $num;
        return $this;
    }

    public function setDialledNumber($num)
    {
        $this->dialled_number = $num;
        return $this;
    }

    public function setDestinationNumber($num)
    {
        $this->destination_number = $num;
        return $this;
    }

    public function setDateStart(DateTime $date)
    {
        $this->date_start = $date;
        return $this;
    }

    public function setDateEnd(DateTime $date)
    {
        $this->date_end = $date;
        return $this;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function setBillDuration($duration)
    {
        $this->bill_duration = $duration;
        return $this;
    }

    public function setBillRate($rate)
    {
        $this->bill_rate = $rate;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setHangupCause($cause)
    {
        $this->hangup_cause = $cause;
        return $this;
    }

    public function setAdvertID($id)
    {
        $this->advert_id = $id;
        return $this;
    }

    public function setAdGroupID($id)
    {
        $this->adgroup_id = $id;
        return $this;
    }

    public function setCampaignID($id)
    {
        $this->campaign_id = $id;
        return $this;
    }

    public function setClientID($id)
    {
        $this->client_id = $id;
        return $this;
    }

    public function setResponseXML($xml)
    {
        $this->response_xml = $xml;
        return $this;
    }

    public function setBHangupCause($cause)
    {
        $this->b_hangup_cause = $cause;
        return $this;
    }

    public function setBStatus($status)
    {
        $this->b_status = $status;
        return $this;
    }

    public function setAudioRecord($audio)
    {
        $this->audio_record = $audio;
        return $this;
    }

    // getters
    public function getID()
    {
        return $this->id;
    }

    public function getDateIn()
    {
        return $this->date_in;
    }

    public function getCallID()
    {
        return $this->call_id;
    }

    public function getOriginNumber()
    {
        return $this->origin_number;
    }

    public function getDialledNumber()
    {
        return $this->dialled_number;
    }

    public function getDestinationNumber()
    {
        return $this->destination_number;
    }

    public function getDateStart()
    {
        return $this->date_start;
    }

    public function getDateEnd()
    {
        return $this->date_end;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getBillDuration()
    {
        return $this->bill_duration;
    }

    public function getBillRate()
    {
        return $this->bill_rate;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getHangupCause()
    {
        return $this->hangup_cause;
    }

    public function getAdvertID()
    {
        return $this->advert_id;
    }

    public function getAdGroupID()
    {
        return $this->adgroup_id;
    }

    public function getCampaignID()
    {
        return $this->campaign_id;
    }

    public function getClientID()
    {
        return $this->client_id;
    }

    public function getResponseXML()
    {
        return $this->response_xml;
    }

    public function getOriginFormatted()
    {
        if ($this->origin_number == 0)
            return 'Anonymous';

        $nf = new NumberFormatter();
        return $nf->format($this->origin_number);
    }

    public function getDialledFormatted()
    {
        if ($this->dialled_number == null)
            return '';

        $nf = new NumberFormatter();
        return $nf->format($this->dialled_number);
    }

    public function getDestinationFormatted()
    {
        if ($this->destination_number == null)
            return '';

        $nf = new NumberFormatter();
        return $nf->format($this->destination_number);
    }

    public function getBStatus()
    {
        return $this->b_status;
    }

    public function getBHangupCause()
    {
        return $this->b_hangup_cause;
    }

    public function getAudioRecord()
    {
        return $this->audio_record;
    }

    public function isFailed()
    {
        switch($this->status)
        {
            case 'busy':
            case 'failed':
            case 'timeout':
            case 'no-answer':
            case 'cancel':
                return true;
        }

        $hc = strtolower($this->hangup_cause);
        if (!empty($hc) && $hc != 'normal_clearing')
            return true;

        $bhc = strtolower($this->b_hangup_cause);
        if (!empty($bhc) && $bhc != 'normal_clearing')
            return true;

        return false;
    }

    // for serialization
    public function getData()
    {
        $data = array(
            'id' => $this->id,
            'date_in' => $this->date_in,
            'call_id' => $this->call_id,
            'origin_number' => $this->origin_number,
            'origin_formatted' => $this->getOriginFormatted(),
            'dialled_number' => $this->dialled_number,
            'dialled_formatted' => $this->getDialledFormatted(),
            'destination_number' => $this->destination_number,
            'destination_formatted' => $this->getDestinationFormatted(),
            'duration' => $this->duration,
            'bill_duration' => $this->bill_duration,
            'bill_rate' => $this->bill_rate,
            'status' => $this->status,
            'hangup_cause' => $this->hangup_cause,
            'advert_id' => $this->advert_id,
            'adgroup_id' => $this->adgroup_id,
            'campaign_id' => $this->campaign_id,
            'client_id' => $this->client_id,
            'b_status' => $this->b_status,
            'b_hangup_cause' => $this->b_hangup_cause
        );

        // date start
        if ($this->date_start != null)
            $data['date_start'] = array(
                'time' => $this->date_start->format('H:i:s'),
                'date' => $this->date_start->format('d M')
            );
        else
            $data['date_start'] = array(
                'time' => '',
                'date' => ''
            );

        // date end
        if ($this->date_end != null)
            $data['date_end'] = array(
                'time' => $this->date_end->format('H:i:s'),
                'date' => $this->date_end->format('d M')
            );
        else
            $data['date_end'] = array(
                'time' => '',
                'date' => ''
            );

        // failed
        $data['failed'] = $this->isFailed();

        return $data;
    }
}
