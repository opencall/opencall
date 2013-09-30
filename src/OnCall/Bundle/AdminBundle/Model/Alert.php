<?php

namespace OnCall\Bundle\AdminBundle\Model;

class Alert
{
    protected $type;
    protected $class;
    protected $message;
    protected $title;

    public function __construct($type)
    {
        $this->setType($type);
        $this->title = null;
        $this->class = '';
        $this->message = '';
    }

    public function setType($type)
    {
        switch ($type)
        {
            case AlertType::WARNING:
                if ($this->title == null)
                    $this->title = 'Warning';
                $this->class = '';
                $this->type = $type;
                break;
            case AlertType::SUCCESS:
                if ($this->title == null)
                    $this->title = 'Success';
                $this->class = 'alert-success';
                $this->type = $type;
                break;
            case AlertType::INFO:
                if ($this->title == null)
                    $this->title = 'Info';
                $this->class = 'alert-info';
                $this->type = $type;
                break;
            case AlertType::ERROR:
                if ($this->title == null)
                    $this->title = 'Error';
                $this->class = 'alert-error';
                $this->type = $type;
                break;
        }

        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getClass()
    {
        return $this->class;
    }
}
