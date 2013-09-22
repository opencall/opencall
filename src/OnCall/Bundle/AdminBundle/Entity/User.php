<?php

namespace OnCall\Bundle\AdminBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $multi_client;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $business_name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=120)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $bill_business_name;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $bill_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $bill_email;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $bill_phone;

    /**
     * @ORM\Column(type="string", length=120)
     */
    protected $bill_address;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_create;


    public function __construct()
    {
        parent::__construct();

        // timestamp
        $this->date_create = new DateTime();
    }

    // begin setters
    public function setMultiClient($multi = true)
    {
        $this->multi_client = $multi;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setBusinessName($name)
    {
        $this->business_name = $name;
        return $this;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setBillBusinessName($name)
    {
        $this->bill_business_name = $name;
        return $this;
    }

    public function setBillName($name)
    {
        $this->bill_name = $name;
        return $this;
    }

    public function setBillEmail($email)
    {
        $this->bill_email = $email;
        return $this;
    }

    public function setBillPhone($phone)
    {
        $this->bill_phone = $phone;
        return $this;
    }
    
    public function setBillAddress($address)
    {
        $this->bill_address = $address;
        return $this;
    }
    // end setters

    // begin getters
    public function isMultiClient()
    {
        return $this->multi_client;
    }

    public function isMultiClientText()
    {
        if ($this->isMultiClient())
            return 'Yes';
        return 'No';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBusinessName()
    {
        return $this->business_name;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getBillBusinessName()
    {
        return $this->bill_business_name;
    }

    public function getBillName()
    {
        return $this->bill_name;
    }

    public function getBillEmail()
    {
        return $this->bill_email;
    }

    public function getBillPhone()
    {
        return $this->bill_phone;
    }

    public function getBillAddress()
    {
        return $this->bill_address;
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    public function getDateCreateFormatted()
    {
        if ($this->date_create == null)
            return '';
        return $this->date_create->format('d M Y');
    }
    // end getters

    public function jsonify()
    {
        $json = array(
            'multi_client' => $this->isMultiClient(),
            'username' => $this->getUsername(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'business_name' => $this->getBusinessName(),
            'phone' => $this->getPhone(),
            'address' => $this->getAddress(),
            'bill_business_name' => $this->getBillBusinessName(),
            'bill_name' => $this->getBillName(),
            'bill_email' => $this->getBillEmail(),
            'bill_phone' => $this->getBillPhone(),
            'bill_address' => $this->getBillAddress(),
            'enable' => $this->isEnabled() ? 1 : 0
        );

        return json_encode($json);
    }

    public function getRoleHash()
    {
        $role_hash = array();
        foreach ($this->roles as $role)
            $role_hash[$role] = true;

        return $role_hash;
    }
}
