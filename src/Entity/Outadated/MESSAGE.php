<?php

namespace Entity\Outadated;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Repository\Outadated\MESSAGERepository;

#[ORM\Entity(repositoryClass: MESSAGERepository::class)]
class MESSAGE
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Date_Msg = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Date_Expi_Msg = null;

    #[ORM\Column(length: 4096)]
    private ?string $Contenu_Msg = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UTILISATEUR $Emetteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UTILISATEUR $Destinataire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateMsg(): ?\DateTimeInterface
    {
        return $this->Date_Msg;
    }

    public function setDateMsg(\DateTimeInterface $Date_Msg): static
    {
        $this->Date_Msg = $Date_Msg;

        return $this;
    }

    public function getDateExpiMsg(): ?\DateTimeInterface
    {
        return $this->Date_Expi_Msg;
    }

    public function setDateExpiMsg(\DateTimeInterface $Date_Expi_Msg): static
    {
        $this->Date_Expi_Msg = $Date_Expi_Msg;

        return $this;
    }

    public function getContenuMsg(): ?string
    {
        return $this->Contenu_Msg;
    }

    public function setContenuMsg(string $Contenu_Msg): static
    {
        $this->Contenu_Msg = $Contenu_Msg;

        return $this;
    }

    public function getEmetteur(): ?UTILISATEUR
    {
        return $this->Emetteur;
    }

    public function setEmetteur(?UTILISATEUR $Emetteur): static
    {
        $this->Emetteur = $Emetteur;

        return $this;
    }

    public function getDestinataire(): ?UTILISATEUR
    {
        return $this->Destinataire;
    }

    public function setDestinataire(?UTILISATEUR $Destinataire): static
    {
        $this->Destinataire = $Destinataire;

        return $this;
    }
}
