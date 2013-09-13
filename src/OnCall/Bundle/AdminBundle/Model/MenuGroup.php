<?php

namespace OnCall\Bundle\AdminBundle\Model;

class MenuGroup implements \Iterator
{
    protected $items;
    protected $item_hash;
    protected $position;
    protected $active;

    public function __construct()
    {
        $this->items = array();
        $this->item_hash = array();
        $this->position = 0;
        $this->active = null;
    }

    public function addItem(MenuItem $item)
    {
        if (isset($this->item_hash[$item->getID()]))
            throw new Exception('Could not add duplicate menu item.');

        $this->items[] = $item;
        $this->item_hash[$item->getID()] = $item;
        return $this;
    }

    protected function resetActive()
    {
        if ($this->active == null)
            return $this;

        $this->item_hash[$this->active]->setActive(false);
        $this->active = null;
        return $this;
    }

    public function setActive($id)
    {
        if ($id == null)
        {
            $this->resetActive();
            return $this;
        }

        // check if it exists
        if (!isset($this->item_hash[$id]))
            return $this;

        $this->item_hash[$id]->setActive();
        $this->active = $id;
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    // iterator
    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }
}
