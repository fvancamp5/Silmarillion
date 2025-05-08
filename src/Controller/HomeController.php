<?php

namespace App\Controller;

use App\Repository\MediasRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;


final class HomeController extends AbstractController{

    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    function index(Request $request, SessionInterface $session): Response {

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias');
        $medias = $response->toArray();
        //dd($medias);
    
        if ($request->getMethod() === 'POST' && $session->get('connexion') === true) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
    
            $data = [
                'email' => $email,
                'password' => $password
            ];
            
            //fait la demande a l'api
            $httpClient = HttpClient::create();
            $response = $httpClient->request('POST', 'http://vm-loulou.van-camp.fr:8000/auth', [
                'json' => $data
            ]);
    
            //verifie que c'est bien un 200
            if ($response->getStatusCode() === 200) {
                $responseData = $response->toArray();
                $jwt = $responseData['token'] ?? null;
    
                if ($jwt) {
                    //donne le token et on le met dans la session
                    $session->set('jwt', $jwt);
                    $userInformations = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/me', [
                        'auth_bearer' => $jwt
                    ]);
                    $userData = $userInformations->toArray();
                    $session->set('user', [
                        'id' => $userData['id'],
                        'email' => $userData['email'],
                        'firstname' => $userData['firstname'],
                        'lastname' => $userData['lastname'],
                        'roles' => $userData['roles']
                    ]);

                    return $this->redirectToRoute('home');
                }
            } else {
                return $this->redirectToRoute('connexion');
            }
        }

        if ($request->getMethod() === 'POST' && $session->get('connexion') === false) {
            //récupère les données du formulaire d'inscription
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $firstname = $request->request->get('firstname');
            $lastname = $request->request->get('lastname');


            $data = [
                'email' => $email,
                'password' => $password,
                'roles' => ['ROLE_USER'],
                'firstname' => $firstname,
                'lastname' => $lastname
            ];
            //fait la demande a l'api
            $httpClient = HttpClient::create();
            $response = $httpClient->request('POST', 'http://vm-loulou.van-camp.fr:8080/api/register', [
                'json' => $data
            ]);

            //verifie que c'est bien un 201
            if ($response->getStatusCode() === 201) {
                $responseData = $response->toArray();

                $session->set('user', [
                    'id' => $responseData['id'],
                    'email' => $responseData['email'],
                    'firstname' => $responseData['firstname'],
                    'lastname' => $responseData['lastname'],
                    'roles' => $responseData['roles']
                ]);
                return $this->redirectToRoute('home');
            
            } 
            else {

                return $this->redirectToRoute('connexion');
            }
        }
        $user = $session->get('user');
        


        return $this->render('home/index.html.twig', [
            'page_name' => 'Accueil',
            'user' => $user,
            'medias' => $medias
        ]);
    }

    //rout pour se delog en renvoyer ver la page d'accueil
    #[Route('/deconnexion', name: 'logout', methods: ['GET'])]
        public function logout(SessionInterface $session): Response {

            //Supprime l'utilisateur de la session
            $session->remove('user');

            return $this->redirectToRoute('home');
        }   
    
}