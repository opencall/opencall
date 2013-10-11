<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="OnCall\Bundle\AdminBundle\Repositories\Counter")
 */
abstract class Counter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="date")
     */
    protected $date_in;

    /**
     * @ORM\Column(type="integer")
     */
    protected $client_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $campaign_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $adgroup_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $advert_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $number_id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $caller_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $count_total;

    /**
     * @ORM\Column(type="integer")
     */
    protected $count_failed;

    /**
     * @ORM\Column(type="integer")
     */
    protected $count_plead;

    /**
     * @ORM\Column(type="integer")
     */
    protected $duration_secs;

    public function __construct()
    {
        $this->date_in = new DateTime();

        $this->count_total = 0;
        $this->count_plead = 0;
        $this->count_failed = 0;
        $this->duration_secs = 0;
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

    // counters
    public function getCountTotal()
    {
        return $this->count_total;
    }

    public function getCountPLead()
    {
        return $this->count_plead;
    }

    public function getCountFailed()
    {
        return $this->count_failed;
    }

    public function getDurationSeconds()
    {
        return $this->duration_secs;
    }

    // duration
    public function getDurationFormatted()
    {
        return $this->formatSeconds($this->duration_secs);
    }

    public function getDurationAverageSeconds()
    {
        // avoid div by 0
        if ($this->count_total == 0)
            return 0;

        return floor($this->duration_secs / $this->counter);
    }

    public function getDurationAverageFormatted()
    {
        return $this->formatSeconds($this->getDurationAverageSeconds());
    }
    // end getters

    public function getData()
    {
        $data = array(
            'date_in' => $this->getDateInFormatted(),
            'count_total' => $this->getCountTotal(),
            'count_plead' => $this->getCountPLead(),
            'count_failed' => $this->getCountFailed(),
            'duration_secs' => $this->getDurationSeconds(),
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
