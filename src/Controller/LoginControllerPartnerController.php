<?php

namespace App\Controller;
use App\Form\LoginPartnerFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LoginControllerPartnerController extends AbstractController

{
    #[Route('/login/partner', name: 'app_login_controller_partner')]
    public function login(Request $request): Response
    {
        $form = $this->createForm(LoginPartnerFormType::class); // Correct class name here

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // Process the form data here
        }

        return $this->render('login_controller_partner/index.html.twig', [
            'loginPartnerForm' => $form->createView(),
        ]);
    }
    }

