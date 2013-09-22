<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 */
class Number
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
    protected $number;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length="50")
     */
    protected $provider;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer")
     */
    protected $price_buy;

    /**
     * @ORM\Column(type="integer")
     */
    protected $price_resale;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_create;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_assign;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_lastcall;

    public function __construct()
    {
        $this->date_create = new DateTime();
    }

    // begin setters
    public function setNumber($num)
    {
        $this->number = $num;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    public function setUser(User $user)
    {
        $tis->user = $user;
        return $this;
    }

    public function setPriceBuy($price)
    {
        $this->price_buy = $price;
        return $this;
    }

    public function setPriceResale($price)
    {
        $this->price_resale = $price;
        return $this;
    }

    public function setDateAssign(DateTime $date)
    {
        $this->date_assign = $date;
        return $this;
    }

    public function setDateLastCall(DateTime $datetime)
    {
        $this->date_lastcall = $datetime;
        return $this;
    }
    // end setters

    // begin getters
    public function getID()
    {
        return $this->id;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPriceBuy()
    {
        return $this->price_buy;
    }

    public function getPriceResale()
    {
        return $this->price_resale;
    }

    public function getDateAssign()
    {
        return $this->date_assign;
    }

    public function getDateLastCall()
    {
        return $this->date_lastcall;
    }
    // end getters
}
