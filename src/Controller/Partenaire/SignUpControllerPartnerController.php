<?php

namespace App\Controller\Partenaire;
use App\Entity\DemandePartenaire;
use App\Entity\Partenaires;
use App\Form\PartenairesTypeForm;
use App\Form\SignUpFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Utilisateurs;
use Gregwar\CaptchaBundle\Validator\Constraints;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Gregwar\CaptchaBundle\Validator\Constraints\Captcha as CaptchaConstraint;

final class SignUpControllerPartnerController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;  // Injection du service logger
    }

    #[Route('/sign-up/partner', name: 'app_sign_up_controller_partner')]
    public function signUp(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new Utilisateurs();
        $form = $this->createForm(SignUpFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getMotDePasse());
            $user->setMotDePasse($hashedPassword);

            // Valeurs par défaut
            $user->setDateInscription(new \DateTime());
            $user->setTypeUtilisateur('partenaire');
            $user->setStatutVerification('en attente');
            $user->setStatus('actif');

            // Enregistrer l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Vérifier que l'ID utilisateur est bien attribué
            if ($user->getIdUtilisateur() === null) {
                $this->addFlash('error', 'Problème d\'enregistrement de l\'utilisateur. Veuillez réessayer.');
                return $this->redirectToRoute('app_sign_up_controller_partner');
            }

            // Stocker l'ID en session
            $session = $request->getSession();
            $session->set('new_partner_user_id', $user->getIdUtilisateur());

            // Ajouter un log pour vérifier l'ID de l'utilisateur dans la session
            $this->logger->info('Utilisateur enregistré avec ID : ' . $user->getIdUtilisateur());

            return $this->redirectToRoute('partenaire_ajouter');
        }

        return $this->render('Partenaire/sign_up_controller_partner/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/partenaire/ajouter', name: 'partenaire_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupération de l'ID utilisateur depuis la session
        $session = $request->getSession();
        $idUtilisateur = $session->get('new_partner_user_id');
    
        if (!$idUtilisateur) {
            $this->addFlash('error', 'Utilisateur introuvable. Veuillez recommencer l’inscription.');
            return $this->redirectToRoute('app_sign_up_controller_partner');
        }
    
        $utilisateur = $entityManager->getRepository(Utilisateurs::class)->find($idUtilisateur);
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur inexistant.');
        }
    
        // Vérifier si un partenaire existe déjà pour cet utilisateur
        $partenaire = $entityManager->getRepository(Partenaires::class)->findOneBy(['utilisateur' => $utilisateur]);
    
        // Si le partenaire n'existe pas, on en crée un nouveau
        if (!$partenaire) {
            $partenaire = new Partenaires();
            $partenaire->setUtilisateur($utilisateur); // Relation ManyToOne
            $partenaire->setEtat(0); // Inactif par défaut
            $partenaire->setDateExpiration(new \DateTime('+1 year'));
        }
    
        $form = $this->createForm(PartenairesTypeForm::class, $partenaire);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload du logo
            $logoFile = $form->get('logo')->getData();
            if ($logoFile) {
                if (!in_array($logoFile->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                    $this->addFlash('error', 'Le fichier du logo doit être une image PNG, JPG ou JPEG.');
                    return $this->redirectToRoute('partenaire_ajouter');
                }
                if ($logoFile->getSize() > 2000000) { // 2MB maximum
                    $this->addFlash('error', 'Le fichier logo est trop volumineux.');
                    return $this->redirectToRoute('partenaire_ajouter');
                }
    
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid() . '.' . $logoFile->guessExtension();
    
                $logoFile->move(
                    $this->getParameter('partners_logos_directory'),
                    $newFilename
                );
    
                $partenaire->setLogo($newFilename);
            }
    
            // Gestion de l'upload des autres documents
            $files = $request->files->get('partenaire')['autres_documents'] ?? [];
            $fileNames = [];
    
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    if ($file->getSize() > 5000000) { // 5MB maximum
                        $this->addFlash('error', 'Un des fichiers joints est trop volumineux.');
                        return $this->redirectToRoute('partenaire_ajouter');
                    }
    
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = uniqid() . '.' . $file->guessExtension();
    
                    $file->move(
                        $this->getParameter('autres_documents_directory'),
                        $newFilename
                    );
    
                    $fileNames[] = $newFilename;
                }
            }
    
            // Enregistrer les fichiers sous forme de JSON
            $partenaire->setAutresDocuments(json_encode($fileNames));
    
            // Enregistrer l'entité Partenaire (mise à jour ou nouvelle création)
            $entityManager->persist($partenaire);
            $entityManager->flush();
    
            // Créer la demande de partenariat
            $demande = new DemandePartenaire();
    
            // Lier à l'ID du partenaire si il existe, sinon lier à l'ID utilisateur
            $demande->setId($partenaire->getIdPartenaire() ?? $utilisateur->getIdUtilisateur());
    
            $demande->setDemandeur($utilisateur->getNom());
            $demande->setTelephone($utilisateur->getTelephone());
            $demande->setEmail($utilisateur->getEmail());
            $demande->setAdresse($utilisateur->getAdresseUtilisateur());
            $demande->setDateDemande(new \DateTime());
            $demande->setEtat('en attente');
            $demande->setDescription('Demande de partenariat pour ' . $partenaire->getNomEntreprise());
            $demande->setTypeActivite('partenaire');
    
            // Enregistrer la demande d'aide
            $entityManager->persist($demande);
            $entityManager->flush();
    
            // Nettoyage session
            $session->remove('new_partner_user_id');
    
            $this->addFlash('success', 'Compte partenaire complété avec succès.');
            return $this->redirectToRoute('app_login_controller_partner');
        }
    
        return $this->render('partenaire/sign_up_controller_partner/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    
}
