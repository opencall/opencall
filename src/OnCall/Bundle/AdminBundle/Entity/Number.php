<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use OnCall\Bundle\AdminBundle\Model\NumberType;

/**
 * @ORM\Entity
 */
class Number
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $provider;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="numbers")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    protected $client;

    /**
     * @ORM\Column(type="integer")
     */
    protected $client_id;

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

    /**
     * @ORM\OneToOne(targetEntity="Advert", mappedBy="number")
     */
    protected $advert;

    public function __construct($id)
    {
        $this->id = $id;
        $this->date_create = new DateTime();
    }

    // begin setters
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

    public function setClient(Client $client)
    {
        $this->client = $client;
        $this->client_id = $client->getID();
        return $this;
    }

    public function setPriceBuy($price)
    {
        // 2 decimal places
        $this->price_buy = round($price * 100);
        return $this;
    }

    public function setPriceResale($price)
    {
        // 2 decimal places
        $this->price_resale = round($price * 100);
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

    public function unassign()
    {
        $this->client = null;
        $this->client_id = null;
        $this->date_assign = null;

        return $this;
    }
    // end setters

    // begin getters
    public function getID()
    {
        return $this->id;
    }

    public function getIDFormatted()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTypeText()
    {
        return NumberType::getName($this->type);
    }

    public function isInUse()
    {
        if ($this->client == null)
            return false;

        return true;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getRawPriceBuy()
    {
        return $this->price_buy;
    }

    public function getRawPriceResale()
    {
        return $this->price_resale;
    }

    public function getPriceBuy()
    {
        return $this->price_buy / 100;
    }

    public function getPriceBuyFormatted()
    {
        return number_format($this->getPriceBuy(), 2);
    }

    public function getPriceResale()
    {
        return $this->price_resale / 100;
    }

    public function getPriceResaleFormatted()
    {
        return number_format($this->getPriceResale(), 2);
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    public function getDateCreateFormatted()
    {
        return $this->date_create->format('d M Y');
    }

    public function getDateAssign()
    {
        return $this->date_assign;
    }

    public function getDateAssignFormatted()
    {
        if ($this->date_assign == null)
            return '-';
        return $this->date_assign->format('d M Y');
    }

    public function getDateLastCall()
    {
        return $this->date_lastcall;
    }

    public function getDateLastCallFormatted()
    {
        if ($this->date_lastcall == null)
            return '-';
        return $this->date_lastcall->format('m/d/y H:i');
    }

    public function getAdvert()
    {
        return $this->advert;
    }

    public function isAssigned()
    {
        if ($this->advert == null)
            return false;

        return true;
    }

    public function getClientFormatted()
    {
        if ($this->getClient() == null)
            return '-';
        return $this->getClient()->getName();
    }

    public function getAdvertFormatted()
    {
        if ($this->getAdvert() == null)
            return '-';
        return $this->getAdvert()->getName();
    }

    public function getAdGroupFormatted()
    {
        if ($this->getAdvert() == null)
            return '-';
        return $this->getAdvert()->getAdGroup()->getName();
    }

    public function getCampaignFormatted()
    {
        if ($this->getAdvert() == null)
            return '-';
        return $this->getAdvert()->getAdGroup()->getCampaign()->getName();
    }

    public function canAssign()
    {
        if ($this->getAdvert() == null)
            return true;

        return false;
    }
    // end getters

    public function jsonify()
    {
        $data = array(
            'id' => $this->getID(),
            'provider' => $this->getProvider(),
            'type' => $this->getType(),
            'price_buy' => $this->getPriceBuy(),
            'price_buy_formatted' => $this->getPriceBuyFormatted(),
            'price_resale' => $this->getPriceResale(),
            'price_resale_formatted' => $this->getPriceResaleFormatted()
        );

        return json_encode($data);
    }
}
