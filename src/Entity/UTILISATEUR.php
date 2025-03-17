<?php

namespace App\Entity;

use App\Repository\UTILISATEURRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UTILISATEURRepository::class)]
class UTILISATEUR implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'id', cascade: ['persist', 'remove'])]
    private ?PRODUCTEUR $producteur = null;

    #[ORM\OneToOne(mappedBy: 'id', cascade: ['persist', 'remove'])]
    private ?ADMINISTRATEUR $administrateur = null;

    #[ORM\Column(length: 50)]
    private ?string $Prenom_Uti = null;

    #[ORM\Column(length: 50)]
    private ?string $Nom_Uti = null;

    #[ORM\Column(length: 50)]
    private ?string $Mail_Uti = null;

    #[ORM\Column(length: 200)]
    private ?string $Adr_Uti = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 50)]
    private ?string $password = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

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

    /**
     * @see PasswordAuthenticatedUserInterface
     */

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $Pwd_Uti): static
    {
        $this->password = $Pwd_Uti;

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

    /**
     * @param list<string> $roles
     */

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->Mail_Uti;
    }

}