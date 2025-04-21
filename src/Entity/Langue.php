<?php

namespace App\Entity;

use App\Repository\LangueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'langue')]
#[ORM\Entity(repositoryClass: LangueRepository::class)]
class Langue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "code_langue", length: 45, nullable: true, options: ["default" => 'NULL'])]
    private ?string $codeLangue = 'NULL';

    #[ORM\Column(name: "nom_langue", length: 450, nullable: true, options: ["default" => 'NULL'])]
    private ?string $nomLangue = 'NULL';

    #[ORM\Column(name: "flag_langue", length: 450, nullable: true, options: ["default" => 'NULL'])]
    private ?string $flagLangue = 'NULL';

    #[ORM\Column(name: "code_route", length: 45, nullable: true, options: ["default" => 'NULL'])]
    private ?string $codeRoute = 'NULL';

    #[ORM\Column(name: "etat", nullable: true, options: ["default" => NULL])]
    private ?int $etat = NULL;

    public function getCodeLangue(): ?string
    {
        return $this->codeLangue;
    }

    public function setCodeLangue(?string $codeLangue): static
    {
        $this->codeLangue = $codeLangue;

        return $this;
    }

    public function getNomLangue(): ?string
    {
        return $this->nomLangue;
    }

    public function setNomLangue(?string $nomLangue): static
    {
        $this->nomLangue = $nomLangue;

        return $this;
    }

    public function getFlagLangue(): ?string
    {
        return $this->flagLangue;
    }

    public function setFlagLangue(?string $flagLangue): static
    {
        $this->flagLangue = $flagLangue;

        return $this;
    }

    public function getCodeRoute(): ?string
    {
        return $this->codeRoute;
    }

    public function setCodeRoute(?string $codeRoute): static
    {
        $this->codeRoute = $codeRoute;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
