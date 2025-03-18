<?php

namespace Entity\Outadated;

use App\Repository\STATUTRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: STATUTRepository::class)]
class STATUT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}