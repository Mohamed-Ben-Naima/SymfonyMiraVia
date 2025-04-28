<?php

namespace App\Controller\Partenaire;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

final class ForgetPasswordPartnerController extends AbstractController
{
    #[Route('/forget/password/partner', name: 'app_forget_password_partner')]
        public function forgotPassword(Request $request): Response
    {
        // Create the form
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => ['placeholder' => 'Entrez votre adresse email'],
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse email est obligatoire.']),
                    new Email(['message' => 'Veuillez fournir une adresse email valide.']),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer le lien de réinitialisation',
                'attr' => ['class' => 'btn btn-primary w-100'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];

            // Logic to handle password reset (e.g., sending email)
            // This is just a placeholder and should be replaced with real logic
            $this->addFlash('success', 'Si cette adresse email est associée à un compte, un lien de réinitialisation vous sera envoyé.');

            return $this->redirectToRoute('app_forget_password_partner');
        }

        return $this->render('/forget_password_partner/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
