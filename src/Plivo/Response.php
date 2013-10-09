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
        // xml header
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

        // response
        $xml .= '<Response>';
        foreach ($this->actions as $act)
            $xml .= $act->renderXML();
        $xml .= '</Response>';

        return $xml;
    }
}
