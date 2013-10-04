<?php

namespace OnCall\Bundle\AdminBundle\Model;

use DateTime;

class AggregateFilter
{
    // item aggregates
    const TYPE_CLIENT                   = 1;
    const TYPE_CLIENT_CHILDREN          = 2;
    const TYPE_CAMPAIGN                 = 3;
    const TYPE_CAMPAIGN_CHILDREN        = 4;
    const TYPE_ADGROUP                  = 5;
    const TYPE_ADGROUP_CHILDREN         = 6;

    // chart aggregates
    const TYPE_DAILY_CLIENT             = 7;
    const TYPE_DAILY_CAMPAIGN           = 8;
    const TYPE_DAILY_ADGROUP            = 9;
    const TYPE_HOURLY_CLIENT            = 10;
    const TYPE_HOURLY_CAMPAIGN          = 11;
    const TYPE_HOURLY_ADGROUP           = 12;

    protected $date_from;
    protected $date_to;
    protected $item_id;
    protected $filter_type;

    public function __construct($filter_type, $item_id)
    {
        $this->filter_type = $filter_type;
        $this->item_id = $item_id;

        // default date is -7 days to today
        $date_now = new DateTime();
        $this->date_to = $this->cleanDate($date_now);
        $this->date_from = $this->cleanDate($date_now);
        $this->date_from->modify('-7 day');
    }

    protected function cleanDate(DateTime $date)
    {
        // strip off time
        return DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . '00:00:00');
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
            case self::TYPE_DAILY_CLIENT:
            case self::TYPE_HOURLY_CLIENT:
                return 'client';
            case self::TYPE_CAMPAIGN:
            case self::TYPE_CAMPAIGN_CHILDREN:
            case self::TYPE_DAILY_CAMPAIGN:
            case self::TYPE_HOURLY_CAMPAIGN:
                return 'campaign';
            case self::TYPE_ADGROUP:
            case self::TYPE_ADGROUP_CHILDREN:
            case self::TYPE_DAILY_ADGROUP:
            case self::TYPE_HOURLY_ADGROUP:
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
            case self::TYPE_DAILY_CLIENT:
            case self::TYPE_HOURLY_CLIENT:
                return 'campaign';
            case self::TYPE_CAMPAIGN:
            case self::TYPE_CAMPAIGN_CHILDREN:
            case self::TYPE_DAILY_CAMPAIGN:
            case self::TYPE_HOURLY_CAMPAIGN:
                return 'adgroup';
            case self::TYPE_ADGROUP:
            case self::TYPE_ADGROUP_CHILDREN:
            case self::TYPE_DAILY_ADGROUP:
            case self::TYPE_HOURLY_ADGROUP:
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

    public function isDaily()
    {
        switch ($this->filter_type)
        {
            case self::TYPE_DAILY_CLIENT:
            case self::TYPE_DAILY_CAMPAIGN:
            case self::TYPE_DAILY_ADGROUP:
                return true;
        }

        return false;
    }

    public function needsChildren()
    {
        switch ($this->filter_type)
        {
            case self::TYPE_CLIENT_CHILDREN:
            case self::TYPE_CAMPAIGN_CHILDREN:
            case self::TYPE_ADGROUP_CHILDREN:
                return true;
        }

        return false;
    }
}
