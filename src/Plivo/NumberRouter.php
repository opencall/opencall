<?php

namespace Plivo;

class NumberRouter
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function resolve($number)
    {
        // TODO: get actions for number from database
        $actions = array();

        return $actions;
    }
}
