<?php

namespace OnCall\Entity;

class RouteAction
{
    const FORWARD           = 1;
    const AUDIO             = 2;
    const HANGUP            = 3;
    const ALERT             = 4;
    const ERROR             = 500;

    protected $env;

    public function __construct($env)
    {
        $this->env = $env;
    }

    public function perform()
    {
    }
}
