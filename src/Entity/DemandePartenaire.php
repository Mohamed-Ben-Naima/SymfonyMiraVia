<?php

namespace App\Entity;

use App\Repository\DemandePartenaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'demande_partenaire')]
#[ORM\Entity(repositoryClass: DemandePartenaireRepository::class)]
class DemandePartenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id",type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "demandeur", length: 100, nullable: true, options: ["default" => 'NULL'])]
    private ?string $demandeur = null;

    #[ORM\Column(name: "telephone", length: 15, nullable: true, options: ["default" => 'NULL'])]
    private ?string $telephone = null;

    #[ORM\Column(name: "email", length: 100, nullable: true, options: ["default" => 'NULL'])]
    private ?string $email = null;

    #[ORM\Column(name: "adresse", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $adresse = null;

    #[ORM\Column(name: "date_demande", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateDemande = null;

    #[ORM\Column(name: "etat", length: 20, nullable: true, options: ["default" => '\'en attente\''])]
    private ?string $etat = '\'en attente\'';

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "preuve", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $preuve = null;

    #[ORM\Column(name: "type_activite", length: 100, nullable: true, options: ["default" => 'NULL'])]
    private ?string $typeActivite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDemandeur(): ?string
    {
        return $this->demandeur;
    }

    public function setDemandeur(?string $demandeur): static
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

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

    public function getPreuve(): ?string
    {
        return $this->preuve;
    }

    public function setPreuve(?string $preuve): static
    {
        $this->preuve = $preuve;

        return $this;
    }

    public function getTypeActivite(): ?string
    {
        return $this->typeActivite;
    }

    public function setTypeActivite(?string $typeActivite): static
    {
        $this->typeActivite = $typeActivite;

        return $this;
    }
}
