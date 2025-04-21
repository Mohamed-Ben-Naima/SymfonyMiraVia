<?php

namespace App\Entity;

use App\Repository\DonsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'dons')]
#[ORM\Entity(repositoryClass: DonsRepository::class)]
class Dons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_don", nullable: true, options: ["default" => NULL])]
    private ?int $idDon = NULL;

    #[ORM\Column(name: "id_client", nullable: true, options: ["default" => NULL])]
    private ?int $idClient = NULL;

    #[ORM\Column(name: "id_partenaire", nullable: true, options: ["default" => NULL])]
    private ?int $idPartenaire = NULL;

    #[ORM\Column(name: "montant", type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ["default" => NULL])]
    private ?string $montant = 'NULL';

    #[ORM\Column(name: "date_don", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateDon = null;

    #[ORM\Column(name: "status", length: 20, nullable: true, options: ["default" => '\'en attente\''])]
    private ?string $status = '\'en attente\'';

    public function getIdDon(): ?int
    {
        return $this->idDon;
    }

    public function setIdDon(?int $idDon): static
    {
        $this->idDon = $idDon;

        return $this;
    }

    public function getIdClient(): ?int
    {
        return $this->idClient;
    }

    public function setIdClient(?int $idClient): static
    {
        $this->idClient = $idClient;

        return $this;
    }

    public function getIdPartenaire(): ?int
    {
        return $this->idPartenaire;
    }

    public function setIdPartenaire(?int $idPartenaire): static
    {
        $this->idPartenaire = $idPartenaire;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDon(): ?\DateTimeInterface
    {
        return $this->dateDon;
    }

    public function setDateDon(?\DateTimeInterface $dateDon): static
    {
        $this->dateDon = $dateDon;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
