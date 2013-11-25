<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Plivo\NumberFormatter;

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

    /**
     * @ORM\Column(type="smallint")
     */
    protected $record;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $speak;

    /**
     * @ORM\Column(type="text")
     */
    protected $speak_message;

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
        return $this;
    }

    public function setRecord($record = true)
    {
        $this->record = $record;
        return $this;
    }

    public function setSpeak($speak = true)
    {
        $this->speak = $speak;
        return $this;
    }

    public function setSpeakMessage($msg)
    {
        $this->speak_message = $msg;
        return $this;
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

        return $this->number->getIDFormatted();
    }

    public function getNumberRaw()
    {
        if ($this->number == null)
            return '';

        return $this->number->getID();
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function getDestinationFormatted()
    {
        if ($this->destination == null)
            return '';

        $nf = new NumberFormatter();
        return $nf->format($this->destination);
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

    public function shouldRecord()
    {
        return $this->record;
    }

    public function shouldSpeak()
    {
        return $this->speak;
    }

    public function getSpeakMessage()
    {
        return $this->speak_message;
    }
    // end getters

    public function getData()
    {
        $data = parent::getData();
        $data['number_id_formatted'] = $this->getNumberFormatted();
        $data['number_id'] = $this->getNumberRaw();
        $data['destination'] = $this->getDestination();
        $data['xml_replace'] = $this->getXMLReplace();
        $data['xml_override'] = $this->shouldXMLOverride();
        $data['record'] = $this->shouldRecord();
        $data['speak'] = $this->shouldSpeak();
        $data['speak_message'] = $this->getSpeakMessage();

        return $data;
    }

    public function unassignNumber()
    {
        $this->number = null;
        return $this;
    }
}
