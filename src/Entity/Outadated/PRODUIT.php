<?php

namespace Entity\Outadated;

use App\Repository\PRODUITRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PRODUITRepository::class)]
class PRODUIT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Nom_Produit = null;

    #[ORM\Column]
    private ?int $Qte_Produit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 5)]
    private ?string $Prix_Produit_Unitaire = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PRODUCTEUR $Id_Prod = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TYPEDEPRODUIT $Id_Type_Produit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UNITE $Id_Unite_Stock = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UNITE $Id_Unite_Prix = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->Nom_Produit;
    }

    public function setNomProduit(string $Nom_Produit): static
    {
        $this->Nom_Produit = $Nom_Produit;

        return $this;
    }

    public function getQteProduit(): ?int
    {
        return $this->Qte_Produit;
    }

    public function setQteProduit(int $Qte_Produit): static
    {
        $this->Qte_Produit = $Qte_Produit;

        return $this;
    }

    public function getPrixProduitUnitaire(): ?string
    {
        return $this->Prix_Produit_Unitaire;
    }

    public function setPrixProduitUnitaire(string $Prix_Produit_Unitaire): static
    {
        $this->Prix_Produit_Unitaire = $Prix_Produit_Unitaire;

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

    public function getIdTypeProduit(): ?TYPEDEPRODUIT
    {
        return $this->Id_Type_Produit;
    }

    public function setIdTypeProduit(?TYPEDEPRODUIT $Id_Type_Produit): static
    {
        $this->Id_Type_Produit = $Id_Type_Produit;

        return $this;
    }

    public function getIdUniteStock(): ?UNITE
    {
        return $this->Id_Unite_Stock;
    }

    public function setIdUniteStock(?UNITE $Id_Unite_Stock): static
    {
        $this->Id_Unite_Stock = $Id_Unite_Stock;

        return $this;
    }

    public function getIdUnitePrix(): ?UNITE
    {
        return $this->Id_Unite_Prix;
    }

    public function setIdUnitePrix(?UNITE $Id_Unite_Prix): static
    {
        $this->Id_Unite_Prix = $Id_Unite_Prix;

        return $this;
    }
}
