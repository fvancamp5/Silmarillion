<?php

namespace App\Controller;

use App\Repository\LoanRepository;
use App\Repository\MediasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;

final class MediasController extends AbstractController{


#[Route('medias/{id}/', name: 'home_id', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    function indexMedia(SessionInterface $session,int $id, Request $request): Response {

        // Récupère l'utilisateur connecté avce la session (pas avec des cookies)
        $user = $session->get('user');
        

        //condition si l'emprunt appartient au user pour afficher le bouton de retour (on met a false de base pour permettre aux user de voir sans se log)
        $condition = false;

    
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias/'.$id);
        $media = $response->toArray();

        if ($user !== null) {
            $loanResponse = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/loans', [
                'query' => [ //filtre les emprunts par user_id et media_id du coup on check s'il est emprunté ou pas
                    'media_id' => $id, 
                    'user_id' => $user['id'],
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $session->get('jwt'),
                ],
            ]);
    
            $loans = $loanResponse->toArray();
            if (!empty($loans)) {
                $condition = true; //change la condition si le user a deja emprunté le media
            }
        }

        
        if ($request->getMethod() === 'POST') {
            if ($user === null) {
                //si le user n'est pas connécté et qu'il veut amprunter, ca lui demande de se connecter
                return $this->redirectToRoute('connexion');
            }
            if ($request->request->get('emprunter')){
                //emprunter le media
                $httpClient = HttpClient::create();
                $response = $httpClient->request('POST', 'http://vm-loulou.van-camp.fr:8000/api/loans', [
                    'json' => [
                        'id_user' => $user['id'],
                        'id_media' => $id,
                        
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer '.$session->get('jwt'),
                        'Content-Type' => 'application/ld+json',
                    ]
                ]);
                if ($response->getStatusCode() == 201) {
                    return $this->redirectToRoute('home_id', ['id' => $id]);
                } 
                
            }
            else if ($request->request->get('retourner')){
                //retourner le media
                $httpClient = HttpClient::create();
                $response = $httpClient->request('DELETE', 'http://vm-loulou.van-camp.fr:8000/api/loans/'.$id, [
                    'headers' => [
                        'Authorization' => 'Bearer '.$session->get('jwt'),
                        'Content-Type' => 'application/ld+json',
                    ]
                ]);
                if ($response->getStatusCode() == 204) {
                    //check à nouveau sinon la page se render avant les modifs sql
                    $mediaResponse = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias/' . $id);
                    $media = $mediaResponse->toArray();
            
                    // check si le media est toujours emprunté ou pas
                    $loanResponse = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/loans', [
                        'query' => [
                            'media_id' => $id,
                            'user_id' => $user['id'],
                        ],
                        'headers' => [
                            'Authorization' => 'Bearer ' . $session->get('jwt'),
                        ],
                    ]);
            
                    $loans = $loanResponse->toArray();
                    $condition = empty($loans); 
                }
            }
        }
        
        return $this->render('medias/index.html.twig', [
            'page_name' => 'Accueil',
            'user' => $user,
            'media' => $media,
            'condition' => $condition,
        ]);
    }

#[Route ('medias/{id}/modification/', name: 'modification', methods: ['GET', 'POST'])]
    function modificationMedia (SessionInterface $session,int $id, Request $request){

        $user = $session->get('user');

        if ($user === null || $user['roles'] == "ROLE_ADMIN") {
            return $this->redirectToRoute('connexion');
        }
        
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias/'.$id);
        $media = $response->toArray();

        //si c en post c'est que c'est le form de modification soumis
        if ($request->getMethod() === 'POST') {
            $title = $request->request->get('title');
            $author = $request->request->get('author');
            $type = $request->request->get('type');
            $description = $request->request->get('description');
            $image = $request->request->get('image');

            //si le form est soumis, on modifie le media
            $httpClient = HttpClient::create();
            $response = $httpClient->request('PATCH', 'http://vm-loulou.van-camp.fr:8000/api/medias/'.$id, [
                'json' => [
                    'title' => $title,
                    'author' => $author,
                    'type' => $type,
                    'description' => $description,
                    'image' => $image,
                ],
                'headers' => [
                    'Authorization' => 'Bearer '.$session->get('jwt'),
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);
            // dd($response->getStatusCode());
            if ($response->getStatusCode() == 200) {
                return $this->redirectToRoute('home_id', ['id' => $id]);
            } else {
                return $this->redirectToRoute('modification', ['id' => $id]);
            }
        }

        return $this->render('medias/modif.html.twig', [
            'page_name' => 'Accueil',
            'user' => $user,
            'media' => $media,
        ]);
    }

#[Route ('medias/{id}/delete/', name: 'suppression', methods: ['GET'])]
    function deleteMedia (SessionInterface $session,int $id, Request $request){

        $user = $session->get('user');
        
        if ($user === null || $user['roles'] == "ROLE_ADMIN") {
            return $this->redirectToRoute('connexion');
        }

        //si le form est soumis on supprime le media
        $httpClient = HttpClient::create();
        $response = $httpClient->request('DELETE', 'http://vm-loulou.van-camp.fr:8000/api/medias/'.$id, [
            'headers' => [
                'Authorization' => 'Bearer '.$session->get('jwt'),
            ],
        ]);
        //si ca marche retour a la page d'accueil sinon on reste sur la page de modif
        if ($response->getStatusCode() == 204) {
            return $this->redirectToRoute('home');
        } 
        else {
            return $this->redirectToRoute('home_id', ['id' => $id]);
        }
    }

#[Route ('/add', name: 'ajout', methods: ['GET', 'POST'])]
    function addMedia (SessionInterface $session, Request $request){

        $user = $session->get('user');
        
        if ($user === null || $user['roles'] == "ROLE_ADMIN") {
            return $this->redirectToRoute('connexion');
        }


        if ($request->getMethod() === 'POST') {
            $title = $request->request->get('title');
            $author = $request->request->get('author');
            $type = $request->request->get('type');
            $description = $request->request->get('description');
            $image = $request->request->get('image');

            //si le form est soumis, on modifie le media
            $httpClient = HttpClient::create();
            $response = $httpClient->request('POST', 'http://vm-loulou.van-camp.fr:8000/api/medias', [
                'json' => [
                    'title' => $title,
                    'author' => $author,
                    'type' => $type,
                    'description' => $description,
                    'image' => $image,
                    'status' => true
                ],
                'headers' => [
                    'Authorization' => 'Bearer '.$session->get('jwt'),
                    'Content-Type' => 'application/ld+json',
                ]
            ]);
            if ($response->getStatusCode() == 201) {
                return $this->redirectToRoute('home');
            } else {
                return $this->redirectToRoute('ajout');
            }
        }

        return $this->render('medias/add.html.twig', [
            'page_name' => 'Ajout Media',
            'user' => $user,
        ]);
    }

}
