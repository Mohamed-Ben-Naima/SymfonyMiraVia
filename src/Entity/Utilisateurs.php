<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'utilisateurs')]
#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
class Utilisateurs implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_utilisateur", nullable: true, options: ["default" => NULL])]
    private ?int $idUtilisateur = NULL;

    #[ORM\Column(name: "nom", length: 50, nullable: true, options: ["default" => 'NULL'])]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $nom = 'Ben Naima';

    #[ORM\Column(name: "prenom", length: 50, nullable: true, options: ["default" => '\'\''])]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $prenom = 'Mohamed';

    #[ORM\Column(name: "email", length: 100, nullable: true, options: ["default" => 'NULL'])]
    #[Assert\Email(
        message: 'L\'email {{ value }} n\'est pas valide.'
    )]
    #[Assert\Length(
        max: 100,
        maxMessage: 'L\'email ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $email = 'example@gmail.com';

    #[ORM\Column(name: "mot_de_passe", length: 255, nullable: true, options: ["default" => 'NULL'])]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $motDePasse = 'password';

    #[ORM\Column(name: "telephone", length: 50, nullable: true, options: ["default" => 'NULL'])]
    private ?string $telephone = '11 111 111';

    #[ORM\Column(name: "date_inscription", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'current_timestamp()'])]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Column(name: "type_utilisateur", length: 20, nullable: true, options: ["default" => 'NULL'])]
    private ?string $typeUtilisateur = 'client';

    #[ORM\Column(name: "avatar", length: 255, nullable: true, options: ["default" => '\'https://example.com/default-avatar.png\''])]
    private ?string $avatar = '\'https://example.com/default-avatar.png\'';

    #[ORM\Column(name: "statut_verification", length: 20, nullable: true, options: ["default" => '\'en attente\''])]
    private ?string $statutVerification = 'en attente';

    #[ORM\Column(name: "status", length: 10, nullable: true, options: ["default" => '\'actif\''])]
    private ?string $status = 'actif';

    #[ORM\Column(name: "birthday", type: Types::DATE_MUTABLE, nullable: true, options: ["default" => 'NULL'])]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(name: "adresse_utilisateur", length: 255, nullable: true, options: ["default" => 'NULL'])]
    private ?string $adresseUtilisateur = 'adresse';

    public function getPassword(): ?string
    {
        // Return the password property (replace 'motDePasse' with your actual password field name)
        return $this->motDePasse;
    }

    public function getId(): ?int
    {
        return $this->getIdUtilisateur();
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

    public function setPassword(?string $password): static
    {
        $this->motDePasse = $password;

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

    /**
     * Required method from UserInterface
     */
    public function getRoles(): array
    {
        // Return an array of roles - at least ROLE_USER
        // You can customize this based on your typeUtilisateur field
        $roles = ['ROLE_USER'];

        if ($this->typeUtilisateur) {
            $roles[] = 'ROLE_' . strtoupper($this->typeUtilisateur);
        }

        return array_unique($roles);
    }

    /**
     * Required method from UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Required method from UserInterface
     * Returns the identifier used for authentication (email)
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }
}
