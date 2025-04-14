<?php

namespace App\Controller;

use Gregwar\Captcha\CaptchaBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CaptchaController extends AbstractController
{
    
    #[Route('/captcha', name: 'captcha')]
    public function generateCaptcha(SessionInterface $session): Response
    {
        // Générer un CAPTCHA
        $builder = new CaptchaBuilder();
        $builder->build();

        // Stocker la phrase CAPTCHA dans la session
        $session->set('captcha', $builder->getPhrase());

        // Retourner l'image CAPTCHA
        $response = new Response($builder->get());
        $response->headers->set('Content-Type', 'image/jpeg');

        return $response;
    }
}