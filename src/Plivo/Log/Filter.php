<?php

namespace Plivo\Log;

class Filter
{
    protected $cid;
    protected $agid;
    protected $adid;
    protected $hcause;
    protected $dmod;
    protected $dsec;
    protected $num;

    public function __construct(
        $cid = '',
        $agid = '',
        $adid = '',
        $hcause = '',
        $dmod = '',
        $dsec = '',
        $num = ''
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
}
