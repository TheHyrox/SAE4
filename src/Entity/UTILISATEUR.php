<?php

namespace App\Entity;

use App\Repository\UTILISATEURRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UTILISATEURRepository::class)]
class UTILISATEUR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    #[ORM\OneToOne(mappedBy: 'Id_Uti', cascade: ['persist', 'remove'])]
    private ?PRODUCTEUR $producteur = null;

    #[ORM\OneToOne(mappedBy: 'Id_Uti', cascade: ['persist', 'remove'])]
    private ?ADMINISTRATEUR $administrateur = null;


    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Prenom_Uti = null;

    #[ORM\Column(length: 50)]
    private ?string $Nom_Uti = null;

    #[ORM\Column(length: 50)]
    private ?string $Mail_Uti = null;

    #[ORM\Column(length: 200)]
    private ?string $Adr_Uti = null;

    #[ORM\Column(length: 50)]
    private ?string $Pwd_Uti = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenomUti(): ?string
    {
        return $this->Prenom_Uti;
    }

    public function setPrenomUti(string $Prenom_Uti): static
    {
        $this->Prenom_Uti = $Prenom_Uti;

        return $this;
    }

    public function getNomUti(): ?string
    {
        return $this->Nom_Uti;
    }

    public function setNomUti(string $Nom_Uti): static
    {
        $this->Nom_Uti = $Nom_Uti;

        return $this;
    }

    public function getMailUti(): ?string
    {
        return $this->Mail_Uti;
    }

    public function setMailUti(string $Mail_Uti): static
    {
        $this->Mail_Uti = $Mail_Uti;

        return $this;
    }

    public function getAdrUti(): ?string
    {
        return $this->Adr_Uti;
    }

    public function setAdrUti(string $Adr_Uti): static
    {
        $this->Adr_Uti = $Adr_Uti;

        return $this;
    }

    public function getPwdUti(): ?string
    {
        return $this->Pwd_Uti;
    }

    public function setPwdUti(string $Pwd_Uti): static
    {
        $this->Pwd_Uti = $Pwd_Uti;

        return $this;
    }

    public function getProducteur(): ?PRODUCTEUR
    {
        return $this->producteur;
    }

    public function setProducteur(?PRODUCTEUR $producteur): static
    {
        $this->producteur = $producteur;
        return $this;
    }

    public function getAdministrateur(): ?ADMINISTRATEUR
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?ADMINISTRATEUR $administrateur): static
    {
        $this->administrateur = $administrateur;
        return $this;
    }
}