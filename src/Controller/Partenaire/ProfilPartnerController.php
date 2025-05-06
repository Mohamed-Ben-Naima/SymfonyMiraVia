<?php

namespace App\Controller\Partenaire;

use App\Entity\Partenaires;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilPartnerController extends AbstractController
{
    #[Route('dashboard/partenaire/paramètres/profil', name: 'app_profil_partner')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }

        // Récupérer les informations du partenaire
        $partenaire = $entityManager->getRepository(Partenaires::class)->findOneBy(['utilisateur' => $user]);

        // Paramètres de répertoires définis dans services.yaml
        $avatarsDirectory = $this->getParameter('avatars_directory');
        $logosDirectory = $this->getParameter('partners_logos_directory');

        // Traitement de l'avatar utilisateur
        $avatarName = $user->getAvatar();
        $avatarPath = $avatarsDirectory . '/' . $avatarName;
        $avatarUrl = '/uploads/avatars/' . (file_exists($avatarPath) ? $avatarName : 'default-avatar.png');

        // Traitement du logo partenaire
        $logoUrl = null;
        if ($partenaire) {
            $logoName = $partenaire->getLogo();
            $logoPath = $logosDirectory . '/' . $logoName;
            $logoUrl = '/uploads/logos/' . (file_exists($logoPath) ? $logoName : 'default-logo.png');
        }

        return $this->render('Partenaire/ProfilePartner/profile-partner.html.twig', [
            'user' => $user,
            'partenaire' => $partenaire,
            'avatarUrl' => $avatarUrl,
            'logoUrl' => $logoUrl,
        ]);
    }
    #[Route('/dashboard/partenaire/avatar', name: 'app_update_avatar', methods: ['POST'])]
    public function updateAvatar(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }
        $avatarFile = $request->files->get('avatar');
    
        if ($avatarFile) {
            $avatarsDirectory = $this->getParameter('avatars_directory');
            $newAvatarName = uniqid() . '.' . $avatarFile->guessExtension();
            $avatarFile->move($avatarsDirectory, $newAvatarName);
            $user->setAvatar($newAvatarName);
            $em->flush();
    
            $this->addFlash('success', 'Avatar mis à jour avec succès.');
        } else {
            $this->addFlash('error', 'Aucun fichier sélectionné pour l’avatar.');
        }
    
        return $this->redirectToRoute('app_profil_partner');
    }
    

#[Route('/dashboard/partenaire/logo', name: 'app_update_logo', methods: ['POST'])]
public function updateLogo(Request $request, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user instanceof Utilisateurs) {
        throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
    }

    $partenaire = $em->getRepository(Partenaires::class)->findOneBy(['utilisateur' => $user]);
    $logoFile = $request->files->get('logo');

    if ($logoFile && $partenaire) {
        $logosDirectory = $this->getParameter('partners_logos_directory');
        $newLogoName = uniqid() . '.' . $logoFile->guessExtension();
        $logoFile->move($logosDirectory, $newLogoName);
        $partenaire->setLogo($newLogoName);
        $em->flush();

        $this->addFlash('success', 'Logo mis à jour avec succès.');
    } else {
        $this->addFlash('error', 'Échec de la mise à jour du logo.');
    }

    return $this->redirectToRoute('app_profil_partner');
}

#[Route('/dashboard/partenaire/password', name: 'app_update_password', methods: ['POST'])]
public function updatePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = $this->getUser();

    if (!$user instanceof Utilisateurs) {
        throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
    }
    $oldPassword = $request->request->get('oldPassword');
    $newPassword = $request->request->get('newPassword');

    if (!$oldPassword || !$newPassword) {
        $this->addFlash('error', 'Veuillez remplir tous les champs.');
        return $this->redirectToRoute('app_profil_partner');
    }

    // Vérifie si le mot de passe est valide
    if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
        $this->addFlash('error', 'Ancien mot de passe incorrect.');
    } elseif ($oldPassword === $newPassword) {
        $this->addFlash('error', 'Le nouveau mot de passe doit être différent de l’ancien.');
    } else {
        $hashedNewPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setMotDePasse($hashedNewPassword);
        $em->flush();

        $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
    }

    return $this->redirectToRoute('app_profil_partner');
}

#[Route('/dashboard/partenaire/profil/update', name: 'app_update_profil', methods: ['POST'])]
public function updateProfil(Request $request, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user instanceof Utilisateurs) {
        throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
    }
    
    $partenaire = $em->getRepository(Partenaires::class)->findOneBy(['utilisateur' => $user]);

    // Récupération des données de la requête
    $nom = $request->get('nom');
    $prenom = $request->get('prenom');
    $telephone = $request->get('telephone');
    $adresse = $request->get('adresse');
    $siteweb = $request->get('siteweb');
    $entreprise = $request->get('entreprise');
    $activite = $request->get('activite');
    $adresseEntreprise = $request->get('adresseEntreprise');
    $telEntreprise = $request->get('tel_entreprise');

    // Mise à jour des informations utilisateur
    $isUpdated = false;

    if (!empty($nom)) {
        $user->setNom($nom);
        $isUpdated = true;
    }
    if (!empty($prenom)) {
        $user->setPrenom($prenom);
        $isUpdated = true;
    }
    if (!empty($telephone)) {
        if (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) {
            $this->addFlash('error', 'Numéro de téléphone utilisateur invalide.');
            return $this->redirectToRoute('app_profil_partner');
        }
        $user->setTelephone($telephone);
        $isUpdated = true;
    }
    
    if (!empty($adresse)) {
        $user->setAdresseUtilisateur($adresse);
        $isUpdated = true;
    }

    // Mise à jour des informations du partenaire
    if ($partenaire) {
        if (!empty($siteweb)) {
            $partenaire->setSiteWeb($siteweb);
            $isUpdated = true;
        }
        if (!empty($entreprise)) {
            $partenaire->setNomEntreprise($entreprise);
            $isUpdated = true;
        }
        if (!empty($activite)) {
            $partenaire->setTypeActivite($activite);
            $isUpdated = true;
        }
        if (!empty($adresseEntreprise)) {
            $partenaire->setAdresse($adresseEntreprise);
            $isUpdated = true;
        }
        if (!empty($telephone)) {
            if (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) {
                $this->addFlash('error', 'Numéro de téléphone utilisateur invalide.');
                return $this->redirectToRoute('app_profil_partner');
            }
            $user->setTelephone($telephone);
            $isUpdated = true;
        }        
    }

    // Sauvegarde des modifications
    if ($isUpdated) {
        $em->persist($user);
        $em->persist($partenaire);
        $em->flush();

        // Message de succès
        $this->addFlash('success', 'Profil mis à jour avec succès');
    } else {
        // Message d'erreur si aucune modification n'a été apportée
        $this->addFlash('error', 'Aucune modification détectée');
    }

    // Redirection après la mise à jour
    return $this->redirectToRoute('app_profil_partner');
}
    }

