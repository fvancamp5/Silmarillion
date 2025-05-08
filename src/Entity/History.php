<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
#[ApiResource(
    operations : [
        new GetCollection(
            uriTemplate: '/histories',
            name: 'get_history',
            controller: 'App\Controller\LoanController::getHistory',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Get all histories',
                    'description' => 'Retrieves all histories.',
                ],
            ],
        ),
    ]
)]
class History
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
