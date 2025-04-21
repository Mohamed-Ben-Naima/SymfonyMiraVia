<?php

namespace App\Entity;

use App\Repository\EvenementsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'evenements')]
#[ORM\Entity(repositoryClass: EvenementsRepository::class)]
class Evenements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_evenement", nullable: true, options: ["default" => NULL])]
    private ?int $idEvenement = NULL;

    #[ORM\Column(name: "titre", length: 100, nullable: true, options: ["default" => 'NULL'])]
    private ?string $titre = 'NULL';

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "date_evenement", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'NULL'])]
    private ?\DateTimeInterface $dateEvenement = null;

    #[ORM\Column(name: "lieu", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $lieu = 'NULL';

    #[ORM\Column(name: "id_partenaire", nullable: true, options: ["default" => NULL])]
    private ?int $idPartenaire = NULL;

    #[ORM\Column(name: "status", length: 20, nullable: true, options: ["default" => '\'actif\''])]
    private ?string $status = '\'actif\'';

    #[ORM\Column(name: "imageName", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $imagename = 'NULL';

    #[ORM\Column(name: "est_bloque", nullable: true, options: ["default" => 0])]
    private ?int $estBloque = 0;

    #[ORM\Column(name: "raisons_blocage", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $raisonsBlocage = 'NULL';

    #[ORM\Column(name: "explication_blocage", type: Types::TEXT, nullable: true)]
    private ?string $explicationBlocage = null;

    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(?int $idEvenement): static
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

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

    public function getDateEvenement(): ?\DateTimeInterface
    {
        return $this->dateEvenement;
    }

    public function setDateEvenement(?\DateTimeInterface $dateEvenement): static
    {
        $this->dateEvenement = $dateEvenement;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getImagename(): ?string
    {
        return $this->imagename;
    }

    public function setImagename(?string $imagename): static
    {
        $this->imagename = $imagename;

        return $this;
    }

    public function getEstBloque(): ?int
    {
        return $this->estBloque;
    }

    public function setEstBloque(?int $estBloque): static
    {
        $this->estBloque = $estBloque;

        return $this;
    }

    public function getRaisonsBlocage(): ?string
    {
        return $this->raisonsBlocage;
    }

    public function setRaisonsBlocage(?string $raisonsBlocage): static
    {
        $this->raisonsBlocage = $raisonsBlocage;

        return $this;
    }

    public function getExplicationBlocage(): ?string
    {
        return $this->explicationBlocage;
    }

    public function setExplicationBlocage(?string $explicationBlocage): static
    {
        $this->explicationBlocage = $explicationBlocage;

        return $this;
    }
}
