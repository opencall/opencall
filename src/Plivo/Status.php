<?php

namespace Plivo;

class Status
{
    const RINGING       = 1;
    const IN_PROGRESS   = 2;
    const COMPLETED     = 3;
    const BUSY          = 4;
    const FAILED        = 5;
    const TIMEOUT       = 6;
    const NO_ANSWER     = 7;
    const CANCEL        = 8;

    protected $num_status;
    protected $text_status;

    public function __construct($text)
    {
        $clean_text = strtolower(trim($text));
        $this->text_status = $clean_text;
        switch ($clean_text)
        {
            case 'ringing':
                $this->num_status = self::RINGING;
                break;
            case 'in-progress':
                $this->num_status = self::IN_PROGRESS;
                break;
            case 'completed':
                $this->num_status = self::COMPLETED;
                break;
            case 'busy':
                $this->num_status = self::BUSY;
                break;
            case 'failed':
                $this->num_status = self::FAILED;
                break;
            case 'timeout':
                $this->num_status = self::TIMEOUT;
                break;
            case 'no-answer':
                $this->num_status = self::NO_ANSWER;
                break;
            case 'cancel':
                $this->num_status = self::CANCEL;
                break;
        }
    }

    public function getStatusNumber()
    {
        return $this->num_status;
    }

    public function getStatusText()
    {
        return $this->text_status;
    }

    public function isFailed()
    {
        switch ($this->num_status)
        {
            case self::BUSY:
            case self::FAILED:
            case self::TIMEOUT:
            case self::NO_ANSWER:
            case self::CANCEL:
                    return true;
        }

        return false;
    }
}
