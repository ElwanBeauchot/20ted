<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $products = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $buyer = null;

    #[ORM\ManyToOne(targetEntity: SecurityUser::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $sender = null;

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

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): static
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

    public function getSender(): ?SecurityUser
    {
        return $this->sender;
    }

    public function setSender(?SecurityUser $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

}
