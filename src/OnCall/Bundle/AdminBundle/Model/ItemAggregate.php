<?php

namespace OnCall\Bundle\AdminBundle\Model;

class ItemAggregate
{
    protected $item_id;
    protected $total;
    protected $plead;
    protected $failed;
    protected $duration;
    protected $unique;


    public function __construct($item_id, $total, $plead, $failed, $duration)
    {
        $this->item_id = $item_id;
        $this->total = $total + 0;
        $this->plead = $plead + 0;
        $this->failed = $failed + 0;
        $this->duration = $duration + 0;

        $this->unique = 0;
    }

    // setters
    public function setUnique($unique)
    {
        $this->unique = $unique;
        return $this;
    }

    // getters
    public function getItemID()
    {
        return $this->item_id;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getPLead()
    {
        return $this->plead;
    }

    public function getFailed()
    {
        return $this->failed;
    }

    public function getTotalFormatted()
    {
        return number_format($this->total);
    }

    public function getPLeadFormatted()
    {
        return number_format($this->plead);
    }

    public function getFailedFormatted()
    {
        return number_format($this->failed);
    }

    public function getUniqueFormatted()
    {
        return number_format($this->unique);
    }

    public function getFailedPercent()
    {
        if ($this->total == 0)
            return 0;

        return $this->failed / $this->total * 100;
    }

    public function getFailedPercentFormatted()
    {
        return number_format($this->getFailedPercent(), 1);
    }

    public function getPLeadPercent()
    {
        if ($this->total == 0)
            return 0;

        return $this->plead / $this->total * 100;
    }

    public function getPLeadPercentFormatted()
    {
        return number_format($this->getPLeadPercent(), 1);
    }

    public function getUniquePercent()
    {
        if ($this->total == 0)
            return 0;

        return $this->unique / $this->total * 100;
    }

    public function getUniquePercentFormatted()
    {
        return number_format($this->getUniquePercent(), 1);
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getDurationAverage()
    {
        // we don't want div by 0
        if ($this->total == 0)
            return 0;

        return $this->duration / $this->total;
    }

    public function getDurationFormatted()
    {
        return $this->formatSeconds($this->duration);
    }

    public function getDurationAverageFormatted()
    {
        return $this->formatSeconds($this->getDurationAverage());
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
