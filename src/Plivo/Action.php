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

    const TYPE_CUSTOM_XML       = 30;

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

    protected function escapeXML($string)
    {
        return htmlspecialchars($string);
    }

    public function renderXML()
    {
        switch ($this->type)
        {
            case self::TYPE_CUSTOM_XML:
                return $this->params['xml'];
            case self::TYPE_DIAL:
                $xml = '<Dial><Number';
                if (isset($this->params['caller_id']))
                    $xml .= ' callerId="' . $this->params['caller_id'] . '"';
                if (isset($this->params['callback_url']))
                    $xml .= ' callbackUrl="' . $this->params['callback_url'] . '"';
                $xml .= '>';
                $xml .= $this->escapeXML($this->params['number']);
                $xml .= '</Number></Dial>';
                return $xml;
            case self::TYPE_SPEAK:
                $xml = '<Speak';
                if (isset($this->params['language']))
                    $xml .= ' language="' . $this->params['language'] . '"';
                $xml .= '>';
                $xml .= $this->escapeXML($this->params['text']);
                $xml .= '</Speak>';
                return $xml;
        }

        return '';
    }
}
