<?php

namespace App\Controller\Partenaire;
use App\Form\LoginPartnerFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LoginControllerPartnerController extends AbstractController
{
    #[Route('/login/partner', name: 'app_login_controller_partner')]
    public function login(Request $request): Response
    {
        // Créez le formulaire
        $form = $this->createForm(LoginPartnerFormType::class);

        // Affichez le formulaire dans la vue
        return $this->render('Partenaire/login_controller_partner/index.html.twig', [
            'loginPartnerForm' => $form->createView(),
        ]);
    }
}