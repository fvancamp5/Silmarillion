<?php

namespace App\Repository;

use App\Entity\Medias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<Medias>
 */
class MediasRepository extends ServiceEntityRepository
{
    private Connection $connection;

    private function containsSpecialChars(string $value): bool {
        //si un caractere n'est pas dans la liste on renvoie true
        if (preg_match('/[^a-zA-Z0-9\s._\'-éàèùçâêîôûäëïöüÿÉÀÈÙÇÂÊÎÔÛÄËÏÖÜŸ-]/u', $value)) { //\pour faire passer ' dans le pregmatch
            return true;
        }
        return false;
    }

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, Medias::class);
        $this->connection = $connection;
    }

    public function update(int $id, string $title, string $author, string $type, string $description, string $image): bool
    {
        if (empty($title) || empty($author) || empty($type) || empty($description) || empty($image) || $this->containsSpecialChars($title) || $this->containsSpecialChars($author) || $this->containsSpecialChars($type) || $this->containsSpecialChars($description) || $this->containsSpecialChars($image)) {
            return false;
        }
        $sql = 'UPDATE medias SET title = :title, author = :author, type = :type, description = :description, image = :image WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('title', $title);
        $stmt->bindValue('author', $author);
        $stmt->bindValue('type', $type);
        $stmt->bindValue('description', $description);
        $stmt->bindValue('image', $image);
        $stmt->bindValue('id', $id);
        $stmt->executeQuery();

        return true;
    }

    public function search(string $query): array
    {
        $sql = 'SELECT * FROM medias WHERE title LIKE :query OR author LIKE :query OR type LIKE :query OR description LIKE :query';
        //on utilise connection a la place de pdo car pas inclus dans doctrine
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('query', '%' . $query . '%');
        $result = $stmt->executeQuery();

        //la methode renvoie un tableau associatif et pas un tableau d'objets
        return $result->fetchAllAssociative(); 
    }

    //verifie si le media est un emprunt du user actuel
    public function isLoan(int $idUser, int $idMedias): bool
    {
        $sql = 'SELECT * FROM loan WHERE id_user = :idUser AND id_media = :idMedias';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('idUser', $idUser);
        $stmt->bindValue('idMedias', $idMedias);
        $result = $stmt->executeQuery();
        return !empty($result->fetchAllAssociative());
    }

    public function add(string $title, string $author, string $type, string $description, string $image): bool
    {
        if (empty($title) || empty($author) || empty($type) || empty($description) || empty($image) || $this->containsSpecialChars($title) || $this->containsSpecialChars($author) || $this->containsSpecialChars($type) || $this->containsSpecialChars($description) || $this->containsSpecialChars($image)) {
            return false;
        }
        $sql = 'INSERT INTO medias (title, author, type, description, image, status) VALUES (:title, :author, :type, :description, :image, 1)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('title', $title);
        $stmt->bindValue('author', $author);
        $stmt->bindValue('type', $type);
        $stmt->bindValue('description', $description);
        $stmt->bindValue('image', $image);
        $stmt->executeQuery();

        return true;
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM medias WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->executeQuery();
    }
}
