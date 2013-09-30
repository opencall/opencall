<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use OnCall\Bundle\AdminBundle\Model\ClientStatus;

/**
 * @ORM\Entity(repositoryClass="OnCall\Bundle\AdminBundle\Repositories\Client")
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    protected $timezone;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_create;

    /**
     * @ORM\OneToMany(targetEntity="Number", mappedBy="client")
     */
    protected $numbers;

    public function __construct()
    {
        $this->status = ClientStatus::ACTIVE;
        $this->date_create = new DateTime();
    }

    // begin setters
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->user_id = $user->getID();
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    // end setters

    // begin getters
    public function getID()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isActive()
    {
        if ($this->status == ClientStatus::ACTIVE)
            return true;

        return false;
    }

    public function isInactive()
    {
        if ($this->status == ClientStatus::INACTIVE)
            return true;

        return false;
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    public function getDateCreateFormatted()
    {
        return $this->date_create->format('d M Y');
    }

    public function getNumbers()
    {
        return $this->numbers;
    }

    public function getNumberCount()
    {
        return count($this->numbers);
    }
    // end getters

    public function jsonify()
    {
        $data = array(
            'id' => $this->getID(),
            'name' => $this->getName(),
            'timezone' => $this->getTimezone(),
            'status' => $this->getStatus(),
            'date_create' => $this->getDateCreateFormatted()
        );

        return json_encode($data);
    }
}
