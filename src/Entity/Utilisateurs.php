<?php

namespace App\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Repository\UtilisateursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'utilisateurs')]
#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id_utilisateur", type: "integer")]
    private ?int $idUtilisateur = null;

    #[ORM\Column(name: "nom", length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(name: "prenom", length: 50, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(name: "email", length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: "mot_de_passe", length: 255, nullable: true)]
    private ?string $motDePasse = null;

    #[ORM\Column(name: "telephone", length: 50, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: "date_inscription", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Column(name: "type_utilisateur", length: 20, nullable: true, options: ["default" => 'NULL'])]
    private ?string $typeUtilisateur =null;

    #[ORM\Column(name: "avatar", length: 255, nullable: true, options: ["default" => '\'https://example.com/default-avatar.png\''])]
    private ?string $avatar = '\'https://example.com/default-avatar.png\'';

    #[ORM\Column(name: "statut_verification", length: 20, nullable: true, options: ["default" => '\'en attente\''])]
    private ?string $statutVerification = '\'en attente\'';

    #[ORM\Column(name: "status", length: 10, nullable: true, options: ["default" => '\'actif\''])]
    private ?string $status = '\'actif\'';

    #[ORM\Column(name: "birthday", type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(name: "adresse_utilisateur", length: 255, nullable: true)]
    private ?string $adresseUtilisateur = null;

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?int $idUtilisateur): static
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

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

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(?string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

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

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(?\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getTypeUtilisateur(): ?string
    {
        return $this->typeUtilisateur;
    }

    public function setTypeUtilisateur(?string $typeUtilisateur): static
    {
        $this->typeUtilisateur = $typeUtilisateur;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getStatutVerification(): ?string
    {
        return $this->statutVerification;
    }

    public function setStatutVerification(?string $statutVerification): static
    {
        $this->statutVerification = $statutVerification;

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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }


    public function getRoles(): array
    {
        $roles = [];
    
        // Ajouter le rôle partenaire si l'utilisateur est de type partenaire
        if ($this->typeUtilisateur === 'partenaire'or $this->typeUtilisateur === 'Partenaire') {
            $roles[] = 'ROLE_PARTENAIRE';
        }
    
        // Ajouter un rôle par défaut
        $roles[] = 'ROLE_USER';
    
        return array_unique($roles); // Supprime les doublons
    }
    public function setBirthday(?\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAdresseUtilisateur(): ?string
    {
        return $this->adresseUtilisateur;
    }

    public function setAdresseUtilisateur(?string $adresseUtilisateur): static
    {
        $this->adresseUtilisateur = $adresseUtilisateur;

        return $this;
    }
     // Methods required by UserInterface

    
 
     public function getSalt(): ?string
     {
         // Not needed when using modern hashers (e.g., bcrypt or Argon2i)
         return null;
     }
 
     public function eraseCredentials(): void
     {
         // If you store any temporary, sensitive data, clear it here
     }
 
     public function getUserIdentifier(): string
     {
         // Return a unique identifier for the user (e.g., email or username)
         return $this->email;
     }
 
     public function getPassword(): ?string
     {
         return $this->motDePasse;
     }
}
