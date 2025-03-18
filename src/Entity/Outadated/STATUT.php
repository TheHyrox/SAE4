<?php

namespace Entity\Outadated;

use Doctrine\ORM\Mapping as ORM;
use Repository\Outadated\STATUTRepository;

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