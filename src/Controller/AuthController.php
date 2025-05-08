<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    #[Route('/sign_in', name: 'connexion')]
    public function signIn(Request $request, SessionInterface $session): Response
    {
        $connexion = true;
        $session->set('connexion', $connexion);

        return $this->render('sign/index.html.twig', [
            'page_name' => 'Connexion',
            'connexion' => $connexion,
        ]);
    }

    #[Route('/sign_up', name: 'inscription')]
    public function signUp(Request $request, SessionInterface $session): Response
    {
        $connexion = false;
        $session->set('connexion', $connexion);

        return $this->render('sign/index.html.twig', [
            'page_name' => 'Inscription',
            'connexion' => $connexion,
        ]);
    }
}
