<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;


final class LoanController extends AbstractController
{
    #[Route('/api/loans', name: 'get_loans', methods: ['GET'])]
    public function getLoans(Connection $connection): JsonResponse
    {
        $sql = 'SELECT * FROM loan';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();
        $loans = $result->fetchAllAssociative();

        return new JsonResponse($loans);
    }

    #[Route('/api/loans', name: 'add_loan', methods: ['POST'])]
    public function addLoan(Request $request, Connection $connection): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $id_user = $data['id_user'] ?? $data['idUser'] ?? null;
        $id_media = $data['id_media'] ?? $data['idMedia'] ?? null;
    
        if (!$id_user || !$id_media) {
            return new JsonResponse(['error' => 'Invalid input'], 400);
        }

        // Check si le media est deja emprunte
        $isLoaned = $connection->fetchOne('SELECT status FROM medias WHERE id = ?', [$id_media]);
        if (!$isLoaned) {
            return new JsonResponse(['error' => 'Media is already loaned'], 400);
        }

        $connection->beginTransaction();

        try {
            //met le media en indisponible 
            $sqlUpdateMedia = 'UPDATE medias SET status = 0 WHERE id = :id_media';
            $stmtUpdateMedia = $connection->prepare($sqlUpdateMedia);
            $stmtUpdateMedia->bindValue('id_media', $id_media);
            $stmtUpdateMedia->executeQuery();

            //ajout aux emprunts
            $sqlInsertLoan = 'INSERT INTO loan (id_user, id_media) VALUES (:id_user, :id_media)';
            $stmtInsertLoan = $connection->prepare($sqlInsertLoan);
            $stmtInsertLoan->bindValue('id_user', $id_user);
            $stmtInsertLoan->bindValue('id_media', $id_media);
            $stmtInsertLoan->executeQuery();

            //si l'emprunt reussi on le met dans la table history
            $sqlInsertHistory = 'INSERT INTO history (id_user, id_media) VALUES (:id_user, :id_media)';
            $stmtInsertHistory = $connection->prepare($sqlInsertHistory);
            $stmtInsertHistory->bindValue('id_user', $id_user);
            $stmtInsertHistory->bindValue('id_media', $id_media);
            $stmtInsertHistory->executeQuery();

            $connection->commit();

            return new JsonResponse(['message' => 'Loan added successfully'], 201);
        } catch (\Exception $e) {
            $connection->rollBack();
            return new JsonResponse(['error' => 'Failed to add loan'], 500);
        }
    }

    #[Route('/api/loans/{id_media}', name: 'return_loan', methods: ['DELETE'])]
    public function returnLoan(string $id_media, Connection $connection): JsonResponse
    {
        $id_media = (int) $id_media;

        // check s'il y a bien un emprunt a retourner
        $sqlCheckLoan = 'SELECT * FROM loan WHERE id_media = :id_media';
        $stmtCheckLoan = $connection->prepare($sqlCheckLoan);
        $stmtCheckLoan->bindValue('id_media', $id_media);
        $loan = $stmtCheckLoan->executeQuery()->fetchAssociative();

        if (!$loan) {
            return new JsonResponse(['error' => 'Loan not found'], 404);
        }

        $connection->beginTransaction();

        try {
            // remet dispo le media
            $sqlUpdateMedia = 'UPDATE medias SET status = 1 WHERE id = :id_media';
            $stmtUpdateMedia = $connection->prepare($sqlUpdateMedia);
            $stmtUpdateMedia->bindValue('id_media', $id_media);
            $stmtUpdateMedia->executeQuery();

            // retire l'emprunt
            $sqlDeleteLoan = 'DELETE FROM loan WHERE id_media = :id_media';
            $stmtDeleteLoan = $connection->prepare($sqlDeleteLoan);
            $stmtDeleteLoan->bindValue('id_media', $id_media);
            $stmtDeleteLoan->executeQuery();

            $connection->commit();

            return new JsonResponse(['message' => 'Loan returned successfully'], 200);
        } catch (\Exception $e) {
            $connection->rollBack();
            return new JsonResponse(['error' => 'Failed to return loan'], 500);
        }
    }
}
