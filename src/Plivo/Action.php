<?php

namespace Plivo;

class Action
{
    const TYPE_SPEAK            = 1;
    const TYPE_PLAY             = 2;
    const TYPE_GET_DIGIT        = 3;
    const TYPE_GET_SPEECH       = 4;
    const TYPE_RECORD           = 5;
    const TYPE_DIAL             = 6;
    const TYPE_CONFERENCE       = 7;
    const TYPE_HANGUP           = 8;
    const TYPE_REDIRECT         = 9;
    const TYPE_SIP_TRANSFER     = 10;
    const TYPE_WAIT             = 11;
    const TYPE_PRE_ANSWER       = 12;

    protected $type;
    protected $params;

    public function __construct($type, $params = array())
    {
        $this->type = $type;
        $this->params = $params;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function renderXML()
    {
        // TODO: render the xml
        return '';
    }
}
