<?php

namespace Plivo\Log;

use DateTime;
use Plivo\Queue\Message;

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

    public function __construct()
    {
        $this->date_in = new DateTime();
    }

    public static function createFromMessage(Message $msg)
    {
        $num_data = $msg->getNumberData();
        $hangup_data = $msg->getHangupParams();

        $entry = new self();

        // number data
        $entry->setClientID($num_data['client_id'])
            ->setCampaignID($num_data['campaign_id'])
            ->setAdGroupID($num_data['adgroup_id'])
            ->setAdvertID($num_data['advert_id'])
            ->setDestinationNumber($num_data['destination']);

        // hangup data
        $entry->setCallID($hangup_data->getUniqueID())
            ->setOriginNumber($hangup_data->getFrom())
            ->setDialledNumber($hangup_data->getTo())
            ->setDuration($hangup_data->getDuration())
            ->setBillDuration($hangup_data->getBillDuration())
            ->setBillRate($hangup_data->getBillRate())
            ->setStatus($hangup_data->getStatus())
            ->setDateStart(new DateTime($hangup_data->getStartTime()))
            ->setDateEnd(new DateTime($hangup_data->getEndTime()))
            ->setHangupCause($hangup_data->getHangupCause());

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

    public function getData()
    {
        $data = array(
            'id' => $this->id,
            'date_in' => $this->date_in,
            'call_id' => $this->call_id,
            'origin_number' => $this->origin_number,
            'dialled_number' => $this->dialled_number,
            'destination_number' => $this->destination_number,
            'duration' => $this->duration,
            'bill_duration' => $this->bill_duration,
            'bill_rate' => $this->bill_rate,
            'status' => $this->status,
            'hangup_cause' => $this->hangup_cause,
            'advert_id' => $this->advert_id,
            'adgroup_id' => $this->adgroup_id,
            'campaign_id' => $this->campaign_id,
            'client_id' => $this->client_id,
        );

        // date start
        if ($this->date_start != null)
            $data['date_start'] = array(
                'time' => $this->date_start->format('H:i:s'),
                'date' => $this->date_start->format('M d Y')
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
                'date' => $this->date_end->format('M d Y')
            );
        else
            $data['date_end'] = array(
                'time' => '',
                'date' => ''
            );

        return $data;
    }
}
