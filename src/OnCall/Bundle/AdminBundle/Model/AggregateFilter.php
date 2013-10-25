<?php

namespace OnCall\Bundle\AdminBundle\Model;

use DateTime;
use DateTimeZone;

class AggregateFilter
{
    // item aggregates
    const TYPE_CLIENT                   = 1;
    const TYPE_CLIENT_CHILDREN          = 2;
    const TYPE_CAMPAIGN                 = 3;
    const TYPE_CAMPAIGN_CHILDREN        = 4;
    const TYPE_ADGROUP                  = 5;
    const TYPE_ADGROUP_CHILDREN         = 6;
    const TYPE_ADVERT                   = 7;
    const TYPE_ADVERT_CHILDREN          = 8;

    // chart aggregates
    const TYPE_DAILY_CLIENT             = 9;
    const TYPE_DAILY_CAMPAIGN           = 10;
    const TYPE_DAILY_ADGROUP            = 11;
    const TYPE_DAILY_ADVERT             = 12;
    const TYPE_HOURLY_CLIENT            = 13;
    const TYPE_HOURLY_CAMPAIGN          = 14;
    const TYPE_HOURLY_ADGROUP           = 15;
    const TYPE_HOURLY_ADVERT            = 16;

    protected $date_from;
    protected $date_to;
    protected $item_id;
    protected $filter_type;

    public function __construct($filter_type, $item_id)
    {
        $this->filter_type = $filter_type;
        $this->item_id = $item_id;

        // default date is -7 days to today
        $date_now = new DateTime('now', new DateTimeZone('Asia/Hong_Kong'));
        $this->date_to = $this->cleanDateTo($date_now);

        $this->date_from = $this->cleanDateFrom($date_now);
        $this->date_from->modify('-7 day');
    }

    protected function cleanDateFrom(DateTime $date)
    {
        // strip off time
        return DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . '00:00:00');
    }

    protected function cleanDateTo(DateTime $date)
    {
        // strip off time
        return DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . '23:59:59');
    }

    // setters
    public function setDateFrom(DateTime $date)
    {
        $this->date_from = $this->cleanDateFrom($date);
        return $this;
    }

    public function setDateTo(DateTime $date)
    {
        $this->date_to = $this->cleanDateTo($date);
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
            case self::TYPE_ADVERT:
            case self::TYPE_ADVERT_CHILDREN:
            case self::TYPE_DAILY_ADVERT:
            case self::TYPE_HOURLY_ADVERT:
                return 'advert';
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
            // advert doesn't have children... yet
        }

        // TODO: error?
        return 'campaign';
    }

    public function getDateFromUTC()
    {
        $date = $this->date_from->format('Y') . ',';
        $date .= ($this->date_from->format('n') - 1) . ',';
        $date .= ($this->date_from->format('j'));
        return 'Date.UTC(' . $date . ')';
    }

    public function getDateToUTC()
    {
        $date = $this->date_to->format('Y') . ',';
        $date .= ($this->date_to->format('n') - 1) . ',';
        $date .= ($this->date_to->format('j'));
        return 'Date.UTC(' . $date . ')';
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
            case self::TYPE_DAILY_ADVERT:
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
            case self::TYPE_ADVERT_CHILDREN:
                return true;
        }

        return false;
    }
}
