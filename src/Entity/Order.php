<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $owner = [];

    /**
     * @ORM\Column(type="string")
     */
    private $orderId = null;

    /**
     * @ORM\Column(type="json")
     */
    private $data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?int
    {
        return $this->orderId;
    }

    public function setOwner(int $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
