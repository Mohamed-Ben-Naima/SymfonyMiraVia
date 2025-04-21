<?php

namespace App\Entity;

use App\Repository\ConversationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'conversations')]
#[ORM\Entity(repositoryClass: ConversationsRepository::class)]
class Conversations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_conversation", nullable: true, options: ["default" => NULL])]
    private ?int $idConversation = NULL;

    #[ORM\Column(name: "sujet", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $sujet = 'NULL';

    #[ORM\Column(name: "date_creation", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: "id_user1", nullable: true, options: ["default" => NULL])]
    private ?int $idUser1 = NULL;

    #[ORM\Column(name: "id_user2", nullable: true, options: ["default" => NULL])]
    private ?int $idUser2 = NULL;

    public function getIdConversation(): ?int
    {
        return $this->idConversation;
    }

    public function setIdConversation(?int $idConversation): static
    {
        $this->idConversation = $idConversation;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(?string $sujet): static
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getIdUser1(): ?int
    {
        return $this->idUser1;
    }

    public function setIdUser1(?int $idUser1): static
    {
        $this->idUser1 = $idUser1;

        return $this;
    }

    public function getIdUser2(): ?int
    {
        return $this->idUser2;
    }

    public function setIdUser2(?int $idUser2): static
    {
        $this->idUser2 = $idUser2;

        return $this;
    }
}
