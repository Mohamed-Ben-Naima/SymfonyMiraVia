<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

final class ResetPasswordPartnerController extends AbstractController
{
    #[Route('/reset/password/partner', name: 'app_reset_password_partner', methods: ['POST', 'GET'])]
    public function resetPassword(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            // Vérifiez si l'email est valide
            if (!$email) {
                $this->addFlash('error', 'Veuillez entrer une adresse email valide.');
                return $this->redirectToRoute('app_reset_password_partner');
            }

            // Générer un mot de passe aléatoire
            $generatedPassword = bin2hex(random_bytes(4)); // Exemple : 8 caractères aléatoires

            // Préparer le contenu HTML de l'email
            $htmlContent = "
                <h1>Réinitialisation de votre mot de passe</h1>
                <p>Bonjour,</p>
                <p>Votre nouveau mot de passe est : <strong>$generatedPassword</strong></p>
                <p>Veuillez vous connecter avec ce mot de passe et le changer immédiatement.</p>
                <p>Cordialement,<br>L'équipe MiraVia</p>
            ";

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
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email.');
            }

            return $this->redirectToRoute('app_login_controller_partner');
        }

        return $this->render('reset_password_partner/index.html.twig', [
            'controller_name' => 'ResetPasswordPartnerController',
        ]);
    }
}