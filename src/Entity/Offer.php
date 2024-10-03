<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $users = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $products = null;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'offer')]
    private Collection $notificationOffer;

    public function __construct()
    {
        $this->notificationOffer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

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

    public function getUsers(): ?SecurityUser
    {
        return $this->users;
    }

    public function setUsers(?SecurityUser $users): static
    {
        $this->users = $users;

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

    /**
     * @return Collection<int, Notification>
     */
    public function getNotificationOffer(): Collection
    {
        return $this->notificationOffer;
    }

    public function addNotificationOffer(Notification $notificationOffer): static
    {
        if (!$this->notificationOffer->contains($notificationOffer)) {
            $this->notificationOffer->add($notificationOffer);
            $notificationOffer->setOffer($this);
        }

        return $this;
    }

    public function removeNotificationOffer(Notification $notificationOffer): static
    {
        if ($this->notificationOffer->removeElement($notificationOffer)) {
            // set the owning side to null (unless already changed)
            if ($notificationOffer->getOffer() === $this) {
                $notificationOffer->setOffer(null);
            }
        }

        return $this;
    }
}
