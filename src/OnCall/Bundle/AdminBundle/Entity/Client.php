<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="OnCall\Bundle\AdminBundle\Repositories\Client")
 */
class Client extends Item
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="clients")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    protected $timezone;

    /**
     * @ORM\OneToMany(targetEntity="Number", mappedBy="client")
     */
    protected $numbers;

    /**
     * @ORM\OneToMany(targetEntity="Campaign", mappedBy="client")
     */
    protected $campaigns;

    /**
     * @ORM\Column(type="integer")
     */
    protected $call_count;

    /**
     * @ORM\Column(type="integer")
     */
    protected $duration;

    // begin setters
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->user_id = $user->getID();
        return $this;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }
    // end setters

    // begin getters
    public function getUser()
    {
        return $this->user;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function getNumbers()
    {
        return $this->numbers;
    }

    public function getNumberCount()
    {
        return count($this->numbers);
    }

    public function getCampaigns()
    {
        return $this->campaigns;
    }

    public function getCampaignCount()
    {
        return count($this->campaigns);
    }

    public function getChildren()
    {
        return $this->getCampaigns();
    }

    public function getCallCount()
    {
        return $this->call_count;
    }

    public function getDuration()
    {
        return $this->duration;
    }
    // end getters

    public function getData()
    {
        $data = parent::getData();
        $data['timezone'] = $this->getTimezone();

        return $data;
    }
}
