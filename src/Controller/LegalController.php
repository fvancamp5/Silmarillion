<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('mention_legales/', name: 'legal', methods: ['GET'])]
    function index(SessionInterface $session): Response {
        $user= null;
        
        // Récupère l'utilisateur connecté avce la session (pas avec des cookies)
        $user = $session->get('user');

        return $this->render('legal/index.html.twig', [
            'page_name' => 'Mentions Légales',
            'user' => $user
        ]);
    }


}
