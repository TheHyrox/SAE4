<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $sendDate = null;

    #[ORM\Column(length: 2048)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'sentMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'receivedMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;
    
    #[ORM\Column(options: ["default" => false])]
    private ?bool $isRead = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSendDate(): ?\DateTimeInterface
    {
        return $this->sendDate;
    }

    public function setSendDate(\DateTimeInterface $sendDate): static
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }
    
    public function isRead(): ?bool
    {
        return $this->isRead;
    }
    
    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
        
        return $this;
    }
}