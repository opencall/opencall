<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use OnCall\Bundle\AdminBundle\Model\ItemStatus;

/**
 * @ORM\MappedSuperclass
 */
abstract class Item
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_create;

    /**
     * @ORM\Column(type="integer")
     */
    protected $count_total;

    /**
     * @ORM\Column(type="integer")
     */
    protected $count_unique;

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
        $this->status = ItemStatus::ACTIVE;
        $this->date_create = new DateTime();

        $this->count_total = 0;
        $this->count_unique = 0;
        $this->count_failed = 0;
        $this->count_plead = 0;

        $this->duration_secs = 0;
    }

    // begin setters
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    // end setters

    // begin getters
    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isActive()
    {
        if ($this->status == ItemStatus::ACTIVE)
            return true;

        return false;
    }

    public function isInactive()
    {
        if ($this->status == ItemStatus::INACTIVE)
            return true;

        return false;
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    public function getDateCreateFormatted()
    {
        return $this->date_create->format('d M Y');
    }

    // counters
    public function getCountTotal()
    {
        return $this->count_total;
    }

    public function getCountUnique()
    {
        return $this->count_unique;
    }

    public function getCountFailed()
    {
        return $this->count_failed;
    }

    public function getCountPLead()
    {
        return $this->count_plead;
    }

    public function getDurationSeconds()
    {
        return $this->duration_secs;
    }

    public function getTotalFormatted()
    {
        return number_format($this->count_total);
    }

    public function getUniqueFormatted()
    {
        return number_format($this->count_unique);
    }

    public function getFailedFormatted()
    {
        return number_format($this->count_failed);
    }

    public function getPLeadFormatted()
    {
        return number_format($this->count_plead);
    }

    public function getUniquePercent()
    {
        if ($this->count_total == 0)
            return 0.0;

        return round($this->count_unique / $this->count_total, 1);
    }

    public function getFailedPercent()
    {
        if ($this->count_total == 0)
            return 0.0;

        return round($this->count_failed / $this->count_total, 1);
    }

    public function getPLeadPercent()
    {
        if ($this->count_total == 0)
            return 0.0;

        return round($this->count_plead / $this->count_total, 1);
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

        return floor($this->duration_secs / $this->count_total);
    }

    public function getDurationAverageFormatted()
    {
        return $this->formatSeconds($this->getDurationAverageSeconds());
    }
    // end getters

    public function jsonify()
    {
        $data = array(
            'id' => $this->getID(),
            'name' => $this->getName(),
            'status' => $this->getStatus(),
            'date_create' => $this->getDateCreateFormatted()
        );

        return json_encode($data);
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
