<?php

namespace Plivo;

class Response
{
    protected $actions;

    public function __construct()
    {
        $this->actions = array();
    }

    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    public function renderXML()
    {
        // TODO: render the xml of the plivo response
        $xml = '';

        return $xml;
    }
}
