<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;

final class MeController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em){
        $this->security = $security;
        $this->em = $em;
    }

    #[Route('api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $userId = $user->getId();
        $connection = $this->em->getConnection();
        $sql = 'SELECT * FROM user WHERE id = :id';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('id', $userId);
        $result = $stmt->executeQuery();
        $data = $result->fetchAssociative();

        if (!$data) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        return new JsonResponse($data, 200, [
            'Content-Type' => 'application/json',
        ]);
            
    }
}
