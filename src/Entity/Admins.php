<?php

namespace App\Entity;

use App\Repository\AdminsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'admins')]
#[ORM\Entity(repositoryClass: AdminsRepository::class)]
class Admins
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_admin", type: "integer")]
    private ?int $idAdmin = null;

    #[ORM\Column(name: "role", length: 50, nullable: true, options: ["default" => null])]
    private ?string $role = null;

    #[ORM\Column(name: "permissions", type: Types::TEXT, nullable: true)]
    private ?string $permissions = null;

    public function getIdAdmin(): ?int
    {
        return $this->idAdmin;
    }

    public function setIdAdmin(?int $idAdmin): static
    {
        $this->idAdmin = $idAdmin;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPermissions(): ?string
    {
        return $this->permissions;
    }

    public function setPermissions(?string $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }
}