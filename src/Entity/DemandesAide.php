<?php

namespace App\Entity;

use App\Repository\DemandesAideRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'demandes_aide')]
#[ORM\Entity(repositoryClass: DemandesAideRepository::class)]
class DemandesAide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_demande", nullable: true, options: ["default" => NULL])]
    private ?int $idDemande = NULL;

    #[ORM\Column(name: "id_client", nullable: true, options: ["default" => NULL])]
    private ?int $idClient = NULL;

    #[ORM\Column(name: "id_partenaire", nullable: true, options: ["default" => NULL])]
    private ?int $idPartenaire = NULL;

    #[ORM\Column(name: "type_aide", length: 20, nullable: true, options: ["default" => 'NULL'])]
    private ?string $typeAide = 'NULL';

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "montant_demande", type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ["default" => NULL])]
    private ?string $montantDemande = 'NULL';

    #[ORM\Column(name: "date_demande", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateDemande = null;

    #[ORM\Column(name: "status", length: 20, nullable: true, options: ["default" => '\'en_attente\''])]
    private ?string $status = '\'en_attente\'';

    #[ORM\Column(name: "preuves", type: Types::TEXT, nullable: true)]
    private ?string $preuves = null;

    public function getIdDemande(): ?int
    {
        return $this->idDemande;
    }

    public function setIdDemande(?int $idDemande): static
    {
        $this->idDemande = $idDemande;

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

    public function getTypeAide(): ?string
    {
        return $this->typeAide;
    }

    public function setTypeAide(?string $typeAide): static
    {
        $this->typeAide = $typeAide;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMontantDemande(): ?string
    {
        return $this->montantDemande;
    }

    public function setMontantDemande(?string $montantDemande): static
    {
        $this->montantDemande = $montantDemande;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(?\DateTimeInterface $dateDemande): static
    {
        $this->dateDemande = $dateDemande;

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

    public function getPreuves(): ?string
    {
        return $this->preuves;
    }

    public function setPreuves(?string $preuves): static
    {
        $this->preuves = $preuves;

        return $this;
    }
}
