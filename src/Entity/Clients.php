<?php

namespace App\Entity;

use App\Repository\ClientsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'clients')]
#[ORM\Entity(repositoryClass: ClientsRepository::class)]
class Clients 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_client", nullable: true, options: ["default" => NULL])]
    private ?int $idClient = NULL;

    #[ORM\Column(name: "adresse", type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "points_fidelite", nullable: true, options: ["default" => 0])]
    private ?int $pointsFidelite = 0;

    #[ORM\Column(name: "photo_CIN", length: 255, nullable: true, options: ["default" => '\'default_value	\''])]
    private ?string $photoCin = '\'default_value	\'';

    #[ORM\Column(name: "autres_documents", type: Types::TEXT, nullable: true)]
    private ?string $autresDocuments = null;

    public function getIdClient(): ?int
    {
        return $this->idClient;
    }

    public function setIdClient(?int $idClient): static
    {
        $this->idClient = $idClient;

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

    public function getPointsFidelite(): ?int
    {
        return $this->pointsFidelite;
    }

    public function setPointsFidelite(?int $pointsFidelite): static
    {
        $this->pointsFidelite = $pointsFidelite;

        return $this;
    }

    public function getPhotoCin(): ?string
    {
        return $this->photoCin;
    }

    public function setPhotoCin(?string $photoCin): static
    {
        $this->photoCin = $photoCin;

        return $this;
    }

    public function getAutresDocuments(): ?string
    {
        return $this->autresDocuments;
    }

    public function setAutresDocuments(?string $autresDocuments): static
    {
        $this->autresDocuments = $autresDocuments;

        return $this;
    }
}
