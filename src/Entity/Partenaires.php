<?php

namespace App\Entity;

use App\Repository\PartenairesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'partenaires')]
#[ORM\Entity(repositoryClass: PartenairesRepository::class)]
class Partenaires
{
    #[ORM\Id]
#[ORM\OneToOne(targetEntity: Utilisateurs::class)]
#[ORM\JoinColumn(name: "id_partenaire", referencedColumnName: "id_utilisateur", nullable: false)]
private ?Utilisateurs $utilisateur = null;

    private ?int $idPartenaire = NULL;

    #[ORM\Column(name: "nom_entreprise", length: 100, nullable: true)]
    private ?string $nomEntreprise = null;

    #[ORM\Column(name: "type_activite", length: 50, nullable: true)]
    private ?string $typeActivite = null;

    #[ORM\Column(name: "site_web", length: 255, nullable: true)]
    private ?string $siteWeb = null;

    #[ORM\Column(name: "adresse", length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "telephone", length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: "autres_documents", type: Types::TEXT, nullable: true)]
    private ?string $autresDocuments = null;

    #[ORM\Column(name: "etat", nullable: true, options: ["default" => 0])]
    private ?int $etat = 0;

    #[ORM\Column(name: "date_expiration", type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateExpiration = null;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $logo = null;

    // Getter et Setter pour $logo
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }
    public function getIdPartenaire(): ?int
    {
        return $this->utilisateur?->getIdUtilisateur();
    }

    public function setIdPartenaire(?int $idPartenaire): static
    {
        $this->idPartenaire = $idPartenaire;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(?string $nomEntreprise): static
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getTypeActivite(): ?string
    {
        return $this->typeActivite;
    }

    public function setTypeActivite(?string $typeActivite): static
    {
        $this->typeActivite = $typeActivite;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(?string $siteWeb): static
    {
        $this->siteWeb = $siteWeb;

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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(?\DateTimeInterface $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }
    public function getUtilisateur(): ?Utilisateurs
{
    return $this->utilisateur;
}

public function setUtilisateur(Utilisateurs $utilisateur): self
{
    $this->utilisateur = $utilisateur;
    return $this;
}

}