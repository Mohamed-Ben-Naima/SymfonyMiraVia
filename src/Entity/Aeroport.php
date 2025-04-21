<?php

namespace App\Entity;

use App\Repository\AeroportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'aeroport')]
#[ORM\Entity(repositoryClass: AeroportRepository::class)]
class Aeroport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", nullable: true, options: ["default" => NULL])]
    private ?int $id = NULL;

    #[ORM\Column(name: "nom", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $nom = 'NULL';

    #[ORM\Column(name: "iata_code", length: 10, nullable: true, options: ["default" => 'NULL'])]
    private ?string $iataCode = 'NULL';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIataCode(): ?string
    {
        return $this->iataCode;
    }

    public function setIataCode(?string $iataCode): static
    {
        $this->iataCode = $iataCode;

        return $this;
    }
}
