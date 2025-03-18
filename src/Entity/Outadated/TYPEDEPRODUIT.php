<?php

namespace Entity\Outadated;

use Doctrine\ORM\Mapping as ORM;
use Repository\Outadated\TYPEDEPRODUITRepository;

#[ORM\Entity(repositoryClass: TYPEDEPRODUITRepository::class)]
class TYPEDEPRODUIT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Desc_Type_Produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescTypeProduit(): ?string
    {
        return $this->Desc_Type_Produit;
    }

    public function setDescTypeProduit(string $Desc_Type_Produit): static
    {
        $this->Desc_Type_Produit = $Desc_Type_Produit;

        return $this;
    }
}
