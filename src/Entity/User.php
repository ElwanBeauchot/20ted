<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private ?float $wallet = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'users', orphanRemoval: true)]
    private Collection $products;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'favorite')]
    private Collection $favorite;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'buyer', orphanRemoval: true)]
    private Collection $orders_buyer;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'seller', orphanRemoval: true)]
    private Collection $orders_seller;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'buyer', orphanRemoval: true)]
    private Collection $messages;

    /**
     * @var Collection<int, Offer>
     */
    #[ORM\OneToMany(targetEntity: Offer::class, mappedBy: 'users', orphanRemoval: true)]
    private Collection $offers;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->favorite = new ArrayCollection();
        $this->orders_buyer = new ArrayCollection();
        $this->orders_seller = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->offers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getWallet(): ?float
    {
        return $this->wallet;
    }

    public function setWallet(float $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setUsers($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUsers() === $this) {
                $product->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getFavorite(): Collection
    {
        return $this->favorite;
    }

    public function addFavorite(Product $favorite): static
    {
        if (!$this->favorite->contains($favorite)) {
            $this->favorite->add($favorite);
            $favorite->addFavorite($this);
        }

        return $this;
    }

    public function removeFavorite(Product $favorite): static
    {
        if ($this->favorite->removeElement($favorite)) {
            $favorite->removeFavorite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrdersBuyer(): Collection
    {
        return $this->orders_buyer;
    }

    public function addOrdersBuyer(Order $ordersBuyer): static
    {
        if (!$this->orders_buyer->contains($ordersBuyer)) {
            $this->orders_buyer->add($ordersBuyer);
            $ordersBuyer->setBuyer($this);
        }

        return $this;
    }

    public function removeOrdersBuyer(Order $ordersBuyer): static
    {
        if ($this->orders_buyer->removeElement($ordersBuyer)) {
            // set the owning side to null (unless already changed)
            if ($ordersBuyer->getBuyer() === $this) {
                $ordersBuyer->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrdersSeller(): Collection
    {
        return $this->orders_seller;
    }

    public function addOrdersSeller(Order $ordersSeller): static
    {
        if (!$this->orders_seller->contains($ordersSeller)) {
            $this->orders_seller->add($ordersSeller);
            $ordersSeller->setSeller($this);
        }

        return $this;
    }

    public function removeOrdersSeller(Order $ordersSeller): static
    {
        if ($this->orders_seller->removeElement($ordersSeller)) {
            // set the owning side to null (unless already changed)
            if ($ordersSeller->getSeller() === $this) {
                $ordersSeller->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setBuyer($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getBuyer() === $this) {
                $message->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setUsers($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getUsers() === $this) {
                $offer->setUsers(null);
            }
        }

        return $this;
    }
}
