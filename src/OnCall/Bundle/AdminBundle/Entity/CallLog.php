<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 */
abstract class CallLog
{
    /**
     * @ORM\Id
     * @ORM\Colunn(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /*
     * @ORM\Column(type="date")
     */
    protected $date_in;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $call_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $origin_number;

    /**
     * @ORM\Column(type="integer")
     */
    protected $dialled_number;

    /**
     * @ORM\Column(type="integer")
     */
    protected $destination_number;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_start;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_end;

    /**
     * @ORM\Column(type="integer")
     */
    protected $duration;

    /**
     * @ORM\Column(type="integer")
     */
    protected $bill_duration;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=5)
     */
    protected $bill_rate;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $hangup_cause;

    /**
     * @ORM\Column(type="integer")
     */
    protected $advert_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $adgroup_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $campaign_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $client_id;

    public function __construct()
    {
        $this->date_in = new DateTime();
    }

    // begin setters
    // end setters

    // begin getters
    public function getDateIn()
    {
        return $this->date_in;
    }

    public function getDateInFormatted()
    {
        return $this->date_in->format('d M Y');
    }

    public function getCallUniqueID()
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

    public function getDurationFormatted()
    {
        return $this->formatSeconds($this->duration_secs);
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

    // end getters

    public function getData()
    {
        $data = array(
            'date_in' => $this->getDateInFormatted(),
            'duration' => $this->getDuration(),
            'duration_formatted' => $this->getDurationFormatted()
        );

        return $data;
    }

    public function jsonify()
    {
        return json_encode($this->getData());
    }

    protected function formatSeconds($seconds)
    {
        $hours = floor($seconds / 3600);
        $remain = $seconds % 3600;
        $mins = floor($remain / 60);
        $secs = $remain % 60;

        return sprintf("%d:%02d:%02d", $hours, $mins, $secs);
    }
}
