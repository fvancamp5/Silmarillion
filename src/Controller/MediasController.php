<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;

#[Route('/api/medias')]
final class MediasController extends AbstractController
{
    private Security $security;
    private Connection $connection;

    public function __construct(Security $security, Connection $connection)
    {
        $this->security = $security;
        $this->connection = $connection;
    }

    #[Route('/{idMedia}', name: 'delete_media', methods: ['DELETE'])]
    public function delete_media(int $idMedia): JsonResponse
    {
        $this->connection->beginTransaction();

        // Supprime les emprunts liés au média supp
        $sqlDeleteLoans = 'DELETE FROM loan WHERE id_media = :id_media';
        $stmtDeleteLoans = $this->connection->prepare($sqlDeleteLoans);
        $stmtDeleteLoans->bindValue('id_media', $idMedia);
        $loanRowsDeleted = $stmtDeleteLoans->executeStatement();

        // Supprime les entrées de l'historique liés au média supp
        $sqlDeleteHistory = 'DELETE FROM history WHERE id_media = :id_media';
        $stmtDeleteHistory = $this->connection->prepare($sqlDeleteHistory);
        $stmtDeleteHistory->bindValue('id_media', $idMedia);
        $historyRowsDeleted = $stmtDeleteHistory->executeStatement();

        // Supprime le média
        $sqlDeleteMedia = 'DELETE FROM medias WHERE id = :id';
        $stmtDeleteMedia = $this->connection->prepare($sqlDeleteMedia);
        $stmtDeleteMedia->bindValue('id', $idMedia);
        $mediaDeleted = $stmtDeleteMedia->executeStatement();

        if ($mediaDeleted === 0) {
            $this->connection->rollBack();
            return new JsonResponse(['message' => 'Media not found'], 404);
        }

        $this->connection->commit();



        return new JsonResponse(['message' => 'Media deleted successfully']);
    }

    #[Route('/', name: 'add_medias', methods: ['POST'])]
    public function add_medias(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['author'], $data['type'], $data['description'])) {
            return new JsonResponse(['message' => 'Invalid input']);
        }

        $sql = 'INSERT INTO medias (title, author, type, description, image, status) VALUES (:title, :author, :type, :description, :image, :status)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('title', $data['title']);
        $stmt->bindValue('author', $data['author']);
        $stmt->bindValue('type', $data['type']);
        $stmt->bindValue('description', $data['description']);
        $stmt->bindValue('image', $data['image']);
        $stmt->bindValue('status', $data['status']); 
        $stmt->executeStatement();

        return new JsonResponse(['message' => 'Media added successfully']);
    }

    #[Route('/search', name: 'search_medias', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('query', '');

        $sql = 'SELECT * FROM medias WHERE title LIKE :query OR author LIKE :query OR type LIKE :query OR description LIKE :query';
        //on utilise connection a la place de pdo car pas inclus dans doctrine
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('query', '%' . $query . '%');
        $result = $stmt->executeQuery();

        //la methode renvoie un tableau associatif et pas un tableau d'objets
        $results = $result->fetchAllAssociative(); 

        return $this->json($results);
    }

    #[Route('/{idMedia}/is-loan', name: 'is_media_loaned', methods: ['GET'])]
    public function isLoan(int $idMedia): JsonResponse
    {
        $user = $this->security->getUser();
        $idUser = $user->getId();
        
        $sql = 'SELECT * FROM loan WHERE id_user = :idUser AND id_media = :idMedias';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('idUser', $idUser);
        $stmt->bindValue('idMedias', $idMedias);
        $result = $stmt->executeQuery();
        return !empty($result->fetchAllAssociative());

        //la methode renvoie un tableau associatif et pas un tableau d'objets
        $isLoaned = $result->fetchAllAssociative();

        return new JsonResponse([
            'isLoaned' => $isLoaned,
        ]);
    }
}