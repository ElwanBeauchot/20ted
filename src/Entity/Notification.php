<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'notificationSeller')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $seller = null;

    #[ORM\ManyToOne(inversedBy: 'notificationBuyer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'notificationProduct')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'notificationOffer')]
    private ?Offer $offer = null;

    #[ORM\Column]
    private ?int $sender = null;
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getBuyer(): ?SecurityUser
    {
        return $this->buyer;
    }

    public function setBuyer(?SecurityUser $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): static
    {
        $this->offer = $offer;

        return $this;
    }

    public function getSender(): ?int
    {
        return $this->sender;
    }

    public function setSender(int $sender): static
    {
        $this->sender = $sender;

        return $this;
    }
}
