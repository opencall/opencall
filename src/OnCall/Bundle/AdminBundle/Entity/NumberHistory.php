<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use OnCall\Bundle\AdminBundle\Model\NumberType;

/**
 * @ORM\Entity
 */
class NumberHistory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $number_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $action;

    /**
     * @ORM\Column("type="datetime")
     */
    protected $date_create;

    public function __construct($num_id, $action)
    {
        $this->number_id = $num_id;
        $this->action = $action;
        $this->date_create = new DateTime();
    }

    // begin getters
    public function getID()
    {
        return $this->id;
    }

    public function getNumberID()
    {
        return $this->number_id;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    public function getDateCreateFormatted()
    {
        return $this->date_create->format('m/d/Y H:i');
    }
    // end getters
}
