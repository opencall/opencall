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

    // begin setters
    public function setCampaign(Campaign $camp)
    {
        $this->campaign = $camp;
        $this->campaign_id = $camp->getID();
        return $this;
    }
    // end setters

    // begin getters
    public function getCampaign()
    {
        return $this->campaign;
    }
    // end getters
}
