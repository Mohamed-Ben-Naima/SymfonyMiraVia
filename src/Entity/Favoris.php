<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'favoris')]
#[ORM\Entity(repositoryClass: FavorisRepository::class)]
class Favoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_favori", nullable: true, options: ["default" => NULL])]
    private ?int $idFavori = NULL;

    #[ORM\Column(name: "id_utilisateur", nullable: true, options: ["default" => NULL])]
    private ?int $idUtilisateur = NULL;

    #[ORM\Column(name: "type_entite", length: 20, nullable: true, options: ["default" => 'NULL'])]
    private ?string $typeEntite = 'NULL';

    #[ORM\Column(name: "id_entite", nullable: true, options: ["default" => NULL])]
    private ?int $idEntite = NULL;

    public function getIdFavori(): ?int
    {
        return $this->idFavori;
    }

    public function setIdFavori(?int $idFavori): static
    {
        $this->idFavori = $idFavori;

        return $this;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?int $idUtilisateur): static
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    public function getTypeEntite(): ?string
    {
        return $this->typeEntite;
    }

    public function setTypeEntite(?string $typeEntite): static
    {
        $this->typeEntite = $typeEntite;

        return $this;
    }

    public function getIdEntite(): ?int
    {
        return $this->idEntite;
    }

    public function setIdEntite(?int $idEntite): static
    {
        $this->idEntite = $idEntite;

        return $this;
    }
}
