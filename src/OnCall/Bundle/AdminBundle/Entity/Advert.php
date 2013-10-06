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

    public function getDestination()
    {
        return $this->destination;
    }
    // end getters

    public function getData()
    {
        $data = parent::getData();
        $data['number_id'] = $this->getNumber->getID();

        return $data;
    }
}
