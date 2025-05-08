<?php

namespace App\Controller;


use App\Repository\HistoryRepository;
use App\Repository\LoanRepository;
use App\Repository\MediasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;

final class AccountController extends AbstractController
{
    #[Route('compte/', name: 'account', methods: ['GET'])]
    function index(SessionInterface $session): Response {
        
        $user = $session->get('user');

        //on renvoi a la page d'accueil si pas de user dans la session
        if ($user === null) {
            return $this->redirectToRoute('home');
        }

        //array pour stocker les medias
        $mediasArray = [];

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/loans');
        $loans = $response->toArray();
        

        foreach ($loans as $loan) {
            //pour chaque emprunt on va chercher le media correspondant
            if ($loan['id_user'] == $user['id'] && !in_Array($loan['id_user'], $mediasArray)) {
                //si le media appartient au user actuel et pas dans le tableau pour eviter les doublons
                $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias/'.$loan['id_media']);
                $media = $response->toArray();
                if ($media) {
                    $mediasArray[] = $media;
                }
            }
        }

        return $this->render('account/index.html.twig', [
            'page_name' => 'Mon compte',
            'user' => $user,
            'medias' => $mediasArray,
        ]);

    }

    #[Route('emprunts/', name: 'history', methods: ['GET'])]
    function loans(SessionInterface $session): Response {
        $user= null;

        $user = $session->get('user');

        if ($user === null) {
            return $this->redirectToRoute('home');
        }

        //array pour stocker les medias
        $mediasArray = [];

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/loans');
        $loans = $response->toArray();
        
        foreach ($loans as $loan) {
            //pour chaque emprunt on va chercher le media correspondant
            if ($loan['id_user'] == $user['id']) {
                $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias/'.$loan['id_media']);
                //dd($response);
                $media = $response->toArray();
                if ($media) {
                    $mediasArray[] = $media;
                }
            }
        }

        $historyArray = [];

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/histories');
        $histories = $response->toArray();

        foreach ($histories as $history) {
            //dd($history);
            //pour chaque emprunt on va chercher le media correspondant
            if ($history['id_user'] == $user['id']) {
                $response = $httpClient->request('GET', 'http://vm-loulou.van-camp.fr:8000/api/medias/'.$history['id_media']);
                //dd($response);
                $media = $response->toArray();
                if ($media) {
                    $historyArray[] = $media;
                }
            }
        }
        
        //dd($historyArray);
        //dd($mediasArray);

        return $this->render('account/history.html.twig', [
            'page_name' => 'Mes emprunts',
            'user' => $user,
            'medias' => $mediasArray,
            'history' => $historyArray,
        ]);
    }
}
