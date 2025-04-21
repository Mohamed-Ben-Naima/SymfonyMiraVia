<?php

namespace App\Entity;

use App\Repository\BenevolatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'benevolat')]
#[ORM\Entity(repositoryClass: BenevolatRepository::class)]
class Benevolat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_benevolat", nullable: true, options: ["default" => NULL])]
    private ?int $idBenevolat = NULL;

    #[ORM\Column(name: "id_partenaire", nullable: true, options: ["default" => NULL])]
    private ?int $idPartenaire = NULL;

    #[ORM\Column(name: "titre", length: 100, nullable: true, options: ["default" => 'NULL'])]
    private ?string $titre = 'NULL';

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "date_benevolat", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'NULL'])]
    private ?\DateTimeInterface $dateBenevolat = null;

    #[ORM\Column(name: "status", length: 20, nullable: true, options: ["default" => '\'ouvert\''])]
    private ?string $status = '\'ouvert\'';

    #[ORM\Column(name: "imageName", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $imagename = 'NULL';

    public function getIdBenevolat(): ?int
    {
        return $this->idBenevolat;
    }

    public function setIdBenevolat(?int $idBenevolat): static
    {
        $this->idBenevolat = $idBenevolat;

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

    public function getDateBenevolat(): ?\DateTimeInterface
    {
        return $this->dateBenevolat;
    }

    public function setDateBenevolat(?\DateTimeInterface $dateBenevolat): static
    {
        $this->dateBenevolat = $dateBenevolat;

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
}
