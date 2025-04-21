<?php

namespace App\Entity;

use App\Repository\CandidaturesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'candidatures')]
#[ORM\Entity(repositoryClass: CandidaturesRepository::class)]
class Candidatures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_candidature", nullable: true, options: ["default" => NULL])]
    private ?int $idCandidature = NULL;

    #[ORM\Column(name: "id_client", nullable: true, options: ["default" => NULL])]
    private ?int $idClient = NULL;

    #[ORM\Column(name: "id_benevolat", nullable: true, options: ["default" => NULL])]
    private ?int $idBenevolat = NULL;

    #[ORM\Column(name: "status", length: 20, nullable: true, options: ["default" => '\'en attente\''])]
    private ?string $status = '\'en attente\'';

    #[ORM\Column(name: "date_candidature", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateCandidature = null;

    public function getIdCandidature(): ?int
    {
        return $this->idCandidature;
    }

    public function setIdCandidature(?int $idCandidature): static
    {
        $this->idCandidature = $idCandidature;

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

    public function getIdBenevolat(): ?int
    {
        return $this->idBenevolat;
    }

    public function setIdBenevolat(?int $idBenevolat): static
    {
        $this->idBenevolat = $idBenevolat;

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

    public function getDateCandidature(): ?\DateTimeInterface
    {
        return $this->dateCandidature;
    }

    public function setDateCandidature(?\DateTimeInterface $dateCandidature): static
    {
        $this->dateCandidature = $dateCandidature;

        return $this;
    }
}
