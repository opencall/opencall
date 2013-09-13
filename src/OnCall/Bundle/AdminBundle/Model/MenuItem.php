<?php

namespace OnCall\Bundle\AdminBundle\Model;

class MenuItem
{
    protected $id;
    protected $icon;
    protected $url;
    protected $name;
    protected $active;
    protected $children;

    public function __construct($id, $name, $url = '#', $icon = 'icon-signal', $active = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
        $this->url = $url;
        $this->setActive($active);
        $this->children = array();
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    public function setURL($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setActive($active = true)
    {
        if ($active)
            $this->active = $active;
        else
            $this->active = false;
        return $this;
    }

    public function addChild(MenuItem $child)
    {
        $this->children[] = $child;
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isActive()
    {
        return $this->active;
    }
}
