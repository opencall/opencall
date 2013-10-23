<?php

namespace Plivo\Log;

class Filter
{
    protected $cid;
    protected $agid;
    protected $adid;
    protected $hcause;

    public function __construct($cid, $agid, $adid, $hcause)
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

        return false;
    }
}
