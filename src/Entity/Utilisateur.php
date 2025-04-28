<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;                                                                                     
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_utilisateur = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 50, options: ['default' => ''])]
    private ?string $prenom = '';

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]

    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'Le mot de passe doit contenir au moins une lettre majuscule.'
    )]
    #[Assert\Regex(
        pattern: '/[0-9]/',
        message: 'Le mot de passe doit contenir au moins un chiffre.'
    )]
    private ?string $mot_de_passe = null;

    #[Assert\NotBlank(message: 'Veuillez confirmer votre mot de passe.')]
    #[Assert\Expression(
        "this.getMotDePasse() === this.getConfirmationMotDePasse()",
        message: "Les mots de passe ne correspondent pas."
    )]
    private ?string $confirmation_mot_de_passe = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[0-9]{8,15}$/',
        message: 'Le numéro de téléphone doit contenir entre 8 et 15 chiffres.'
    )]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $date_inscription = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(choices: ['admin', 'partenaire', 'client'], message: 'Type utilisateur invalide.')]
    private ?string $type_utilisateur = null;

    #[ORM\Column(type: 'string', length: 255, options: ['default' => 'https://example.com/default-avatar.png'])]
    private ?string $avatar = 'https://example.com/default-avatar.png';

    #[ORM\Column(type: 'string', length: 50, options: ['default' => 'en attente'])]
    #[Assert\Choice(choices: ['en attente', 'validé', 'refusé ou bloqué'], message: 'Statut de vérification invalide.')]
    private ?string $statut_verification = 'en attente';

    #[ORM\Column(type: 'string', length: 50, options: ['default' => 'actif'])]
    #[Assert\Choice(choices: ['actif', 'inactif'], message: 'Statut utilisateur invalide.')]
    private ?string $status = 'actif';

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: 'La date doit être valide et au format attendu.'
    )]
    #[Assert\LessThanOrEqual(
        'today',
        message: 'La date de naissance ne peut pas être dans le futur.'
    )]
    
    #[Assert\LessThanOrEqual(
        'today',
        message: 'La date de naissance ne peut pas être dans le futur.'
    )]
    private ?\DateTimeInterface $birthday = null;
        
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresse_utilisateur = null;



    // Getters and Setters...

    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    public function getConfirmationMotDePasse(): ?string
    {
        return $this->confirmation_mot_de_passe;
    }

    public function setConfirmationMotDePasse(?string $confirmation_mot_de_passe): self
    {
        $this->confirmation_mot_de_passe = $confirmation_mot_de_passe;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }

    public function getTypeUtilisateur(): ?string
    {
        return $this->type_utilisateur;
    }

    public function setTypeUtilisateur(string $type_utilisateur): self
    {
        $this->type_utilisateur = $type_utilisateur;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getStatutVerification(): ?string
    {
        return $this->statut_verification;
    }

    public function setStatutVerification(string $statut_verification): self
    {
        $this->statut_verification = $statut_verification;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function getAdresseUtilisateur(): ?string
    {
        return $this->adresse_utilisateur;
    }

    public function setAdresseUtilisateur(?string $adresse_utilisateur): self
    {
        $this->adresse_utilisateur = $adresse_utilisateur;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Clear temporary sensitive data
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom . ' (' . $this->email . ')';
    }
}