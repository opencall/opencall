<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Campaign extends Item
{
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="campaigns")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    protected $client;

    /**
     * @ORM\Column(type="integer")
     */
    protected $client_id;

    // begin setters
    public function setClient(Client $client)
    {
        $this->client = $client;
        $this->client_id = $client->getID();
        return $this;
    }
    // end setters

    // begin getters
    public function getClient()
    {
        return $this->client;
    }
    // end getters
}
