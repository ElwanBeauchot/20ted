<?php

namespace App\Entity;

use App\Repository\SecurityUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: SecurityUserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private ?float $wallet = 0;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'seller', orphanRemoval: true)]
    private Collection $notificationSeller;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'buyer', orphanRemoval: true)]
    private Collection $notificationBuyer;

    public function __construct()
    {
        $this->notificationSeller = new ArrayCollection();
        $this->notificationBuyer = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotificationSeller(): Collection
    {
        return $this->notificationSeller;
    }

    public function addNotificationSeller(Notification $notificationSeller): static
    {
        if (!$this->notificationSeller->contains($notificationSeller)) {
            $this->notificationSeller->add($notificationSeller);
            $notificationSeller->setSeller($this);
        }

        return $this;
    }

    public function removeNotificationSeller(Notification $notificationSeller): static
    {
        if ($this->notificationSeller->removeElement($notificationSeller)) {
            // set the owning side to null (unless already changed)
            if ($notificationSeller->getSeller() === $this) {
                $notificationSeller->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotificationBuyer(): Collection
    {
        return $this->notificationBuyer;
    }

    public function addNotificationBuyer(Notification $notificationBuyer): static
    {
        if (!$this->notificationBuyer->contains($notificationBuyer)) {
            $this->notificationBuyer->add($notificationBuyer);
            $notificationBuyer->setBuyer($this);
        }

        return $this;
    }

    public function removeNotificationBuyer(Notification $notificationBuyer): static
    {
        if ($this->notificationBuyer->removeElement($notificationBuyer)) {
            // set the owning side to null (unless already changed)
            if ($notificationBuyer->getBuyer() === $this) {
                $notificationBuyer->setBuyer(null);
            }
        }

        return $this;
    }
}
