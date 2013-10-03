<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\MappedSuperclass
 */
abstract class Item
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
     * @ORM\Column(type="integer")
     */
    protected $counter;

    /**
     * @ORM\Column(type="integer")
     */
    protected $duration_secs;

    public function __construct()
    {
        $this->date_in = new DateTime();

        $this->counter = 0;
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
    public function getCounter()
    {
        return $this->counter;
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
            'counter' => $this->getCounter(),
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
