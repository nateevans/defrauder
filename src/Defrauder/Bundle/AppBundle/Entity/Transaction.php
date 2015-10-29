<?php

namespace Defrauder\Bundle\AppBundle\Entity;

use Defrauder\Bundle\AppBundle\Helper\DefrauderHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Defrauder\Bundle\AppBundle\Entity\TransactionRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="transaction")
 * @todo In real world, this would be associated to a User entity
 */
class Transaction implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $uuid;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $amount;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $address;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $city;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $state;

    /**
     * @var int
     * @ORM\Column(type="integer", length=5)
     */
    protected $zip;

    public function __construct()
    {
    }

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getCreatedString()
    {
        return $this->created->format('Y-m-d H:i:s');
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param int $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return array(
          "uuid"    => $this->getUuid(),
          "name"    => $this->getName(),
          "created" => $this->getCreatedString(),
          "amount"  => $this->getAmount(),
          "address" => $this->getAddress(),
          "city"    => $this->getCity(),
          "state"   => $this->getState(),
          "zip"     => $this->getZip()
        );
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function transactionUpdate()
    {
        $now = new \DateTime("now");
        if ($this->getCreated() == null) {
            $this->setCreated($now);
        }

        if ($this->getUuid() == null) {
            $this->setUuid(DefrauderHelper::guidv4());
        }
    }
}