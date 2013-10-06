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
    // end getters
}
