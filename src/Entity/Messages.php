<?php

namespace App\Entity;
use App\Entity\Utilisateurs;
use App\Repository\MessagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'messages')]
#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{ 
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id_message", type: "integer")]
    private ?int $idMessage = null;

    #[ORM\Column(name: "id_expediteur", nullable: true)]
    private ?int $idExpediteur = NULL;

    #[ORM\Column(name: "id_destinataire", nullable: true)]
    private ?int $idDestinataire = NULL;

    #[ORM\Column(name: "message", type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(name: "reponse_chatbot", type: Types::TEXT, nullable: true)]
    private ?string $reponseChatbot = null;

    #[ORM\Column(name: "date_envoi", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\Column(name: "id_conversation", nullable: true)]
    private ?int $idConversation = NULL;

    #[ORM\Column(name: "type_message", type: Types::STRING, nullable: true)]
    private ?string $typeMessage = '\'utilisateur\'';

    #[ORM\Column(name: "statut", type: Types::STRING, nullable: true)]
    private ?string $statut = '\'envoyé\'';
    // Dans Messages.php
    #[ORM\ManyToOne(targetEntity: Conversations::class)]
    #[ORM\JoinColumn(name: "id_conversation", referencedColumnName: "id_conversation", nullable: true)]
    private ?Conversations $conversation = null;
    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: "id_expediteur", referencedColumnName: "id_utilisateur")]
    private ?Utilisateurs $expediteur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: "id_destinataire", referencedColumnName: "id_utilisateur")]
    private ?Utilisateurs $destinataire = null;

    public function getExpediteur(): ?Utilisateurs
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Utilisateurs $expediteur): static
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getDestinataire(): ?Utilisateurs
    {
        return $this->destinataire;
    }

    public function setDestinataire(?Utilisateurs $destinataire): static
    {
        $this->destinataire = $destinataire;

        return $this;
    }
    public function getIdMessage(): ?int
    {
        return $this->idMessage;
    }

    public function setIdMessage(int $idMessage): static
    {
        $this->idMessage = $idMessage;

        return $this;
    }

    public function getIdExpediteur(): ?int
    {
        return $this->idExpediteur;
    }

    public function setIdExpediteur(?int $idExpediteur): static
    {
        $this->idExpediteur = $idExpediteur;

        return $this;
    }

    public function getIdDestinataire(): ?int
    {
        return $this->idDestinataire;
    }

    public function setIdDestinataire(?int $idDestinataire): static
    {
        $this->idDestinataire = $idDestinataire;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getReponseChatbot(): ?string
    {
        return $this->reponseChatbot;
    }

    public function setReponseChatbot(?string $reponseChatbot): static
    {
        $this->reponseChatbot = $reponseChatbot;

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

    public function getIdConversation(): ?int
    {
        return $this->idConversation;
    }

    public function setIdConversation(?int $idConversation): static
    {
        $this->idConversation = $idConversation;

        return $this;
    }

    public function getTypeMessage(): ?string
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(?string $typeMessage): static
    {
        $this->typeMessage = $typeMessage;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
     // Getter et setter pour la relation avec Conversation
     public function getConversation(): ?Conversations
     {
         return $this->conversation;
     }
 
     public function setConversation(?Conversations $conversation): self
     {
         $this->conversation = $conversation;
         return $this;
     }
 
}
