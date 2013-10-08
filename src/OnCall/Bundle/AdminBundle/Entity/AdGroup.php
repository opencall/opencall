<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AdGroup extends Item
{
    /**
     * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="adgroups")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     */
    protected $campaign;

    /**
     * @ORM\Column(type="integer")
     */
    protected $campaign_id;

    /**
     * @ORM\OneToMany(targetEntity="Advert", mappedBy="adgroup")
     */
    protected $adverts;

    // begin setters
    public function setCampaign(Campaign $camp)
    {
        $this->campaign = $camp;
        $this->campaign_id = $camp->getID();
        return $this;
    }

    public function setParent(Campaign $camp)
    {
        return $this->setCampaign($camp);
    }
    // end setters

    // begin getters
    public function getCampaign()
    {
        return $this->campaign;
    }

    public function getAdverts()
    {
        return $this->adverts;
    }

    public function getUser()
    {
        return $this->getCampaign()->getUser();
    }

    public function getParent()
    {
        return $this->getCampaign();
    }

    public function getChildren()
    {
        return $this->getAdverts();
    }
    // end getters
}
