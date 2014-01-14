<?php

namespace Plivo\Log;

use DateTime;
use DateTimeZone;

class Filter
{
    protected $cid;
    protected $agid;
    protected $adid;
    protected $hcause;
    protected $dmod;
    protected $dsec;
    protected $num;
    protected $dts;
    protected $dte;

    public function __construct(
        $cid = '',
        $agid = '',
        $adid = '',
        $hcause = '',
        $dmod = '',
        $dsec = '',
        $num = '',
        $failed = '',
        $dts = '',
        $dte = ''
    )
    {
        if ($cid == '')
            $this->cid = null;
        else
            $this->cid = $cid;

        if ($agid == '')
            $this->agid = null;
        else
            $this->agid = $agid;

        if ($adid == '')
            $this->adid = null;
        else
            $this->adid = $adid;

        if ($hcause == '')
            $this->hcause = null;
        else
            $this->hcause = $hcause;

        if ($dmod == '')
            $this->dmod = null;
        else
            $this->dmod = $dmod;

        if ($dsec == '')
            $this->dsec = null;
        else
            $this->dsec = $dsec;

        if ($num == '')
            $this->num = null;
        else
            $this->num = $num;

        if ($failed == 1)
            $this->failed = true;
        else
            $this->failed = false;

        // dates
        $tz = new DateTimeZone('Asia/Hong_Kong');

        // date start
        try
        {
            if ($dts != '')
                $this->dts = new DateTime($dts, $tz);
            else
            {
                // default to 7 days ago
                $this->dts = new DateTime(null, $tz);
                $this->dts->modify('-6 day');
            }
        }
        catch (\Exception $e)
        {
            $this->dts = new DateTime(null, $tz);
            $this->dts->modify('-6 day');
        }
        $this->cleanDTS();


        // date end
        try
        {
            if ($dte != '')
                $this->dte = new DateTime($dte, $tz);
            else
                $this->dte = new DateTime(null, $tz);
        }
        catch (\Exception $e)
        {
            $this->dte = new DateTime(null, $tz);
        }
        $this->cleanDTE();
    }

    protected function cleanDTS()
    {
        $this->dts = DateTime::createFromFormat('Y-m-d H:i:s', $this->dts->format('Y-m-d') . ' 00:00:00');
    }

    protected function cleanDTE()
    {
        $this->dte = DateTime::createFromFormat('Y-m-d H:i:s', $this->dte->format('Y-m-d') . ' 23:59:59');
    }

    public function getCID()
    {
        return $this->cid;
    }

    public function getAGID()
    {
        return $this->agid;
    }

    public function getAdID()
    {
        return $this->adid;
    }

    public function getHCause()
    {
        return $this->hcause;
    }

    public function getDMod()
    {
        return $this->dmod;
    }

    public function getDSec()
    {
        return $this->dsec;
    }

    public function getNum()
    {
        return $this->num;
    }

    public function isFailed()
    {
        return $this->failed;
    }

    public function canReset()
    {
        if ($this->cid != null)
            return true;

        if ($this->agid != null)
            return true;

        if ($this->adid != null)
            return true;

        if ($this->hcause != null)
            return true;

        if ($this->dmod != null)
            return true;

        if ($this->dsec != null)
            return true;

        if ($this->num != null)
            return true;

        return false;
    }

    public function getDTS()
    {
        return $this->dts;
    }

    public function getDTE()
    {
        return $this->dte;
    }

    public function getDTSUTC()
    {
        $date = $this->dts->format('Y') . ',';
        $date .= ($this->dts->format('n') - 1) . ',';
        $date .= ($this->dts->format('j'));
        return 'Date.UTC(' . $date . ')';
    }

    public function getDTEUTC()
    {
        $date = $this->dte->format('Y') . ',';
        $date .= ($this->dte->format('n') - 1) . ',';
        $date .= ($this->dte->format('j'));
        return 'Date.UTC(' . $date . ')';
    }

    public function getDTSFormatted()
    {
        if ($this->dts == null)
            return '';

        return $this->dts->format('Y-m-d');
    }

    public function getDTEFormatted()
    {
        if ($this->dte == null)
            return '';

        return $this->dte->format('Y-m-d');
    }

    public function toData()
    {
        return array(
            'campaign_id' => $this->cid,
            'adgroup_id' => $this->agid,
            'advert_id' => $this->adid,
            'hangup_cause' => $this->hcause,
            'duration_mod' => $this->dmod,
            'duration_secs' => $this->dsec,
            'number' => $this->num,
            'failed' => $this->failed,
            'dts_utc' => $this->getDTSUTC(),
            'dte_utc' => $this->getDTEUTC(),
            'dts' => $this->getDTSFormatted(),
            'dte' => $this->getDTEFormatted()
        );
    }

    public function getDTSLocale1()
    {
        return 'idate.' . strtolower($this->dts->format('F'));
    }

    public function getDTSLocale2()
    {
        return $this->dts->format('j, Y');
    }

    public function getDTELocale1()
    {
        return 'idate.' . strtolower($this->dte->format('F'));
    }

    public function getDTELocale2()
    {
        return $this->dte->format('j, Y');
    }
}
