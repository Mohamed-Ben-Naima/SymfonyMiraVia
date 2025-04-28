<?php

namespace App\Controller\Partenaire;

use App\Form\ForgetPasswordPartnerFormType;
use App\Entity\Utilisateurs;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

final class ForgetPasswordPartnerController extends AbstractController
{
    #[Route('/forget/password/partner', name: 'app_forget_password_partner')]
    public function forgotPassword(Request $request, MailerInterface $mailer, LoggerInterface $logger, ManagerRegistry $doctrine): Response
    {
        // Création du formulaire ForgetPasswordPartnerFormType
        $form = $this->createForm(ForgetPasswordPartnerFormType::class);
        $form->handleRequest($request);

        // Debug de la configuration MAILER_DSN
        dump($_ENV['MAILER_DSN']);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            // Vérifiez si l'utilisateur existe
            $user = $doctrine->getRepository(Utilisateurs::class)
                ->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cette adresse email.');
                return $this->redirectToRoute('app_forget_password_partner');
            }

            // Générer un mot de passe aléatoire
            $generatedPassword = bin2hex(random_bytes(4)); // Exemple : 8 caractères aléatoires

            // Mettre à jour le mot de passe de l'utilisateur
            $user->setMotDePasse(password_hash($generatedPassword, PASSWORD_BCRYPT));
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            // Préparer le contenu HTML de l'email avec Twig
            $htmlContent = $this->renderView('emails/forget_password.html.twig', [
                'user' => $user,
                'password' => $generatedPassword,
            ]);

            // Créer l'email
            $emailMessage = (new Email())
                ->from('mayssajrad2002@gmail.com') // Adresse email de l'expéditeur
                ->to($email) // Adresse email du destinataire
                ->subject('Réinitialisation de votre mot de passe')
                ->html($htmlContent); // Contenu HTML

            // Envoyer l'email
            try {
                $mailer->send($emailMessage);
                $this->addFlash('success', 'Un email avec votre nouveau mot de passe a été envoyé.');
            } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
                // Logger l'erreur et afficher un message à l'utilisateur
                $logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email.');
            }

            // Rediriger vers la page de connexion
            return $this->redirectToRoute('app_login_controller_partner');
        }

        // Afficher le formulaire
        return $this->render('Partenaire/forget_password_partner/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}