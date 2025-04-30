<?php

namespace App\Entity;

use App\Repository\NotificationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'notifications')]
#[ORM\Entity(repositoryClass: NotificationsRepository::class)]
class Notifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id_notification", type: "integer")] 
    #[Groups(['notif:read'])]
    private ?int $idNotification = NULL;

    #[ORM\Column(name: "id_utilisateur", nullable: true, options: ["default" => NULL])]
    #[Groups(['notif:read'])]
    private ?int $idUtilisateur = NULL;

    #[ORM\Column(name: "message", type: Types::TEXT, nullable: true)]
    #[Groups(['notif:read'])]
    private ?string $message = null;

    #[ORM\Column(name: "type_notification", length: 20, nullable: true, options: ["default" => 'NULL'])]
    #[Groups(['notif:read'])]
    private ?string $typeNotification = 'NULL';

    #[ORM\Column(name: "date_envoi", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    #[Groups(['notif:read'])]
    private ?\DateTimeInterface $dateEnvoi = null;

    public function getIdNotification(): ?int
    {
        return $this->idNotification;
    }

    public function setIdNotification(?int $idNotification): static
    {
        $this->idNotification = $idNotification;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getTypeNotification(): ?string
    {
        return $this->typeNotification;
    }

    public function setTypeNotification(?string $typeNotification): static
    {
        $this->typeNotification = $typeNotification;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }
}
