<?php

namespace Entity\Outadated;

use Doctrine\ORM\Mapping as ORM;
use Repository\Outadated\UNITERepository;

#[ORM\Entity(repositoryClass: UNITERepository::class)]
class UNITE
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Nom_UNITE = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomUNITE(): ?string
    {
        return $this->Nom_UNITE;
    }

    public function setNomUNITE(string $Nom_UNITE): static
    {
        $this->Nom_UNITE = $Nom_UNITE;

        return $this;
    }
}