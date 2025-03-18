<?php

namespace Entity\Outadated;

use App\Repository\COMMANDERepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: COMMANDERepository::class)]
class COMMANDE
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?STATUT $Id_Statut = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PRODUCTEUR $Id_Prod = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UTILISATEUR $Id_Uti = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdStatut(): ?STATUT
    {
        return $this->Id_Statut;
    }

    public function setIdStatut(?STATUT $Id_Statut): static
    {
        $this->Id_Statut = $Id_Statut;

        return $this;
    }

    public function getIdProd(): ?PRODUCTEUR
    {
        return $this->Id_Prod;
    }

    public function setIdProd(?PRODUCTEUR $Id_Prod): static
    {
        $this->Id_Prod = $Id_Prod;

        return $this;
    }

    public function getIdUti(): ?UTILISATEUR
    {
        return $this->Id_Uti;
    }

    public function setIdUti(?UTILISATEUR $Id_Uti): static
    {
        $this->Id_Uti = $Id_Uti;

        return $this;
    }
}
