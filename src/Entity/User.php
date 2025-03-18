<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Unique;
#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'user')]
    private Collection $products;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?TypeProfession $profession = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private Collection $sentMessages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Message::class)]
    private Collection $receivedMessages;

    /**
     * @var Collection<int, Command>
     */
    #[ORM\OneToMany(targetEntity: Command::class, mappedBy: 'customer')]
    private Collection $commands;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->commands = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

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
            $product->setUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }

        return $this;
    }

    public function getProfession(): ?TypeProfession
    {
        return $this->profession;
    }

    public function setProfession(?TypeProfession $profession): static
    {
        $this->profession = $profession;

        return $this;
    }

    public function getMessages(): ?Message
    {
        return $this->messages;
    }

    public function setMessages(Message $messages): static
    {
        // set the owning side of the relation if necessary
        if ($messages->getSender() !== $this) {
            $messages->setSender($this);
        }

        $this->messages = $messages;

        return $this;
    }

    public function getReceivedMessage(): ?Message
    {
        return $this->receivedMessage;
    }

    public function setReceivedMessage(Message $receivedMessage): static
    {
        // set the owning side of the relation if necessary
        if ($receivedMessage->getRecipient() !== $this) {
            $receivedMessage->setRecipient($this);
        }

        $this->receivedMessage = $receivedMessage;

        return $this;
    }

    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): static
    {
        if (!$this->commands->contains($command)) {
            $this->commands->add($command);
            $command->setCustomer($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): static
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getCustomer() === $this) {
                $command->setCustomer(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }
}