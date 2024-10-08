<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order_table`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\OneToOne(inversedBy: 'orders', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $products = null;

    #[ORM\ManyToOne(inversedBy: 'orders_buyer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'orders_seller')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $seller = null;

    #[ORM\Column]
    private ?bool $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(Product $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function getBuyer(): ?SecurityUser
    {
        return $this->buyer;
    }

    public function setBuyer(?SecurityUser $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getSeller(): ?SecurityUser
    {
        return $this->seller;
    }

    public function setSeller(?SecurityUser $seller): static
    {
        $this->seller = $seller;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }
}
