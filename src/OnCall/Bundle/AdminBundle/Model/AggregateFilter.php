<?php

namespace OnCall\Bundle\AdminBundle\Model;

use DateTime;

class AggregateFilter
{
    const TYPE_CLIENT                   = 1;
    const TYPE_CLIENT_CHILDREN          = 2;
    const TYPE_CAMPAIGN                 = 3;
    const TYPE_CAMPAIGN_CHILDREN        = 4;
    const TYPE_ADGROUP                  = 5;
    const TYPE_ADGROUP_CHILDREN         = 6;

    protected $date_from;
    protected $date_to;
    protected $item_id;
    protected $filter_type;

    public function __construct($filter_type)
    {
        $this->filter_type = $filter_type;

        // default date is -7 days to today
        $date_now = new DateTime();
        $this->date_to = $this->cleanDate($date_now);
        $this->date_from = $this->cleanDate($date_now);
        $this->date_from->modify('-7 day');
    }

    protected function cleanDate(DateTime $date)
    {
        // strip off hours and minutes
        return DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d H') . ':00:00');
    }

    // setters
    public function setDateFrom(DateTime $date)
    {
        $this->date_from = $this->cleanDate($date);
        return $this;
    }

    public function setDateTo(DateTime $date)
    {
        $this->date_to = $this->cleanDate($date);
        return $this;
    }

    public function setFilterType($type)
    {
        $this->filter_type = $type;
        return $this;
    }

    public function setItemID($item_id)
    {
        $this->item_id = $item_id;
        return $this;
    }

    // getters
    public function getDateFrom()
    {
        return $this->date_from;
    }

    public function getDateTo()
    {
        return $this->date_to;
    }

    public function getFilterType()
    {
        return $this->filter_type;
    }

    public function getItemID()
    {
        return $this->item_id;
    }

    public function getItemType()
    {
        // get item type based on filter type
        switch ($this->filter_type)
        {
            case self::TYPE_CLIENT:
            case self::TYPE_CLIENT_CHILDREN:
                return 'client';
            case self::TYPE_CAMPAIGN:
            case self::TYPE_CAMPAIGN_CHILDREN:
                return 'campaign';
            case self::TYPE_ADGROUP:
            case self::TYPE_ADGROUP_CHILDREN:
                return 'adgroup';
        }

        // TODO: error?
        return 'client';
    }

    public function getChildrenType()
    {
        // get item type based on filter type
        switch ($this->filter_type)
        {
            case self::TYPE_CLIENT:
            case self::TYPE_CLIENT_CHILDREN:
                return 'campaign';
            case self::TYPE_CAMPAIGN:
            case self::TYPE_CAMPAIGN_CHILDREN:
                return 'adgroup';
            case self::TYPE_ADGROUP:
            case self::TYPE_ADGROUP_CHILDREN:
                return 'advert';
        }

        // TODO: error?
        return 'campaign';
    }

    public function getDateFromFormatted()
    {
        return $this->date_from->format('F j, Y');
    }

    public function getDateToFormatted()
    {
        return $this->date_to->format('F j, Y');
    }
}
