<?php

namespace App\Controller;

use App\Repository\MediasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;

final class SearchController extends AbstractController
{
    #[Route('/recherche', name: 'search', methods: ['GET', 'POST'])]
    public function index(SessionInterface $session, Request $request): Response {
        // Récupère l'utilisateur connecté avce la session (pas avec des cookies)
        $user = $session->get('user');
        $itemsPerPage = $request->query->get('itemsPerPage', 'tout');
        $page = (int) $request->query->get('page', 1);
        $query = null;
        $results = [];
        
        if ($request->isMethod('POST')) {
            $query = $request->request->get('query');
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias/search', [
                'query' => [
                    'query' => $query,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $session->get('jwt'),
                ],
            ]);
            $results = $response->toArray();
        }
        

        if ($itemsPerPage !== 'tout') {
            $itemsPerPage = (int) $itemsPerPage;
            $offset = ($page - 1) * $itemsPerPage;
            $paginatedResults = array_slice($results, $offset, $itemsPerPage);
        } else {
            $paginatedResults = $results; 
        }

        return $this->render('search/index.html.twig', [
            'page_name' => 'Recherche',
            'medias' => $paginatedResults,
            'user' => $user,
            'query' => $query,
            'itemsPerPage' => $itemsPerPage,
            'page' => $page,
            'totalResults' => count($results),
        ]);
    }
}
