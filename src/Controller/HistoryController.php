<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\DBAL\Connection;

final class HistoryController extends AbstractController
{
    #[Route('/api/histories', name: 'get_history', methods: ['GET'])]
    public function getHistory(Connection $connection): JsonResponse
    {
        $sql = 'SELECT * FROM history';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();
        $history = $result->fetchAllAssociative();

        return new JsonResponse($history);
    }
}
