<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/loans',
            name: 'get_loans',
            controller: 'App\Controller\LoanController::getLoans',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Get all loans',
                    'description' => 'Retrieves all loans.',
                ],
            ],
        ),
        new Post(
            uriTemplate: '/loans',
            name: 'add_loan',
            controller: 'App\Controller\LoanController::addLoan',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Add a loan',
                    'description' => 'Adds a new loan for a media item.',
                ],
            ],
        ),
        new Delete(
            uriTemplate: '/loans/{idMedia}',
            name: 'return_loan',
            controller: 'App\Controller\LoanController::returnLoan',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Return a loan',
                    'description' => 'Returns a loan for a media item.',
                ],
            ],
        ),
    ]
)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column]
    private ?int $id_media = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdMedia(): ?int
    {
        return $this->id_media;
    }

    public function setIdMedia(int $id_media): static
    {
        $this->id_media = $id_media;

        return $this;
    }
}
