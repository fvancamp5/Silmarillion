<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class RegisterController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        Connection $connection, 
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate input data
        if (!isset($data['email'], $data['password'], $data['roles'], $data['firstname'], $data['lastname'])) {
            return new JsonResponse(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
        }

        // si le mail est deja, on ne peut pas le rajouter
        $existingUser = $connection->fetchOne(
            'SELECT COUNT(*) FROM user WHERE email = ?',
            [$data['email']]
        );

        if ($existingUser > 0) {
            return new JsonResponse(['error' => 'User already exists'], Response::HTTP_CONFLICT);
        }

        //cree un objet User
        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles($data['roles']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);

        // Hash en bcrypt
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // ajout a la db
        $connection->insert('user', [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'roles' => json_encode($user->getRoles()), 
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ]);

        return new JsonResponse(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }
}