<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Advert extends Item
{
    /**
     * @ORM\ManyToOne(targetEntity="AdGroup", inversedBy="adverts")
     * @ORM\JoinColumn(name="adgroup_id", referencedColumnName="id")
     */
    protected $adgroup;

    /**
     * @ORM\Column(type="integer")
     */
    protected $adgroup_id;

    /**
     * @ORM\OneToOne(targetEntity="Number", inversedBy="advert")
     * @ORM\JoinColumn(name="number_id", referencedColumnName="id")
     */
    protected $number;

    /**
     * @ORM\Column(type="integer")
     */
    protected $destination;

    /**
     * @ORM\Column(type="text")
     */
    protected $xml_replace;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $xml_override;

    public function __construct()
    {
        parent::__construct();
        $this->xml_override = 0;
    }

    // begin setters
    public function setAdGroup(AdGroup $adg)
    {
        $this->adgroup = $adg;
        $this->adgroup_id = $adg->getID();
        return $this;
    }

    public function setParent(AdGroup $adg)
    {
        return $this->setAdGroup($adg);
    }

    public function setNumber(Number $number = null)
    {
        $this->number = $number;
        return $this;
    }

    public function setDestination($dest)
    {
        $this->destination = $dest;
        return $this;
    }

    public function setXMLReplace($xml)
    {
        $this->xml_replace = $xml;
        return $this;
    }

    public function setXMLOverride($override = true)
    {
        $this->xml_override = $override;
        return true;
    }
    // end setters

    // begin getters
    public function getAdGroup()
    {
        return $this->adgroup;
    }

    public function getParent()
    {
        return $this->getAdGroup();
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getNumberFormatted()
    {
        if ($this->number == null)
            return '';

        return $this->number->getID();
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function getXMLReplace()
    {
        return $this->xml_replace;
    }

    public function hasXMLReplace()
    {
        if (strlen(trim($this->xml_replace)) > 0)
            return true;
        return false;
    }

    public function shouldXMLOverride()
    {
        return $this->xml_override;
    }
    // end getters

    public function getData()
    {
        $data = parent::getData();
        $data['number_id'] = $this->getNumberFormatted();
        $data['destination'] = $this->getDestination();
        $data['xml_replace'] = $this->getXMLReplace();
        $data['xml_override'] = $this->shouldXMLOverride();

        return $data;
    }

    public function unassignNumber()
    {
        $this->number = null;
        return $this;
    }
}
