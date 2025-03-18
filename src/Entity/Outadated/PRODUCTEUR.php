<?php

namespace Entity\Outadated;

use App\Repository\PRODUCTEURRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PRODUCTEURRepository::class)]
class PRODUCTEUR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Prof_Prod = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UTILISATEUR $Id_Uti = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProf_Prod(): ?string
    {
        return $this->Prof_Prod;
    }

    public function setProfProd(string $Prof_Prod): static
    {
        $this->Prof_Prod = $Prof_Prod;

        return $this;
    }

    public function getIdUti(): ?UTILISATEUR
    {
        return $this->Id_Uti;
    }

    public function setIdUti(UTILISATEUR $Id_Uti): static
    {
        $this->Id_Uti = $Id_Uti;

        return $this;
    }
}