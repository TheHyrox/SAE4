<?php

namespace App\Entity;

use App\Repository\ADMINISTRATEURRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ADMINISTRATEURRepository::class)]
class ADMINISTRATEUR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UTILISATEUR $Id_Uti = null;

    public function getId(): ?int
    {
        return $this->id;
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