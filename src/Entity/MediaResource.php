<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/medias/search',
            controller: 'App\Controller\MediasController::search',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Search medias',
                    'description' => 'Search for medias by title, author, type, or description.',
                    'parameters' => [
                        [
                            'name' => 'q',
                            'in' => 'query',
                            'required' => true,
                            'description' => 'The search query.',
                            'schema' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Search results',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'integer'],
                                                'title' => ['type' => 'string'],
                                                'author' => ['type' => 'string'],
                                                'type' => ['type' => 'string'],
                                                'description' => ['type' => 'string'],
                                                'status' => ['type' => 'boolean'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ),
        new Get(
            uriTemplate: '/medias/{idMedia}/is-loan',
            controller: 'App\Controller\MediasController::isLoan',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Check if a media is loaned',
                    'description' => 'Checks if a specific media is currently loaned by the authenticated user.',
                    'responses' => [
                        '200' => [
                            'description' => 'Loan status',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'isLoaned' => ['type' => 'boolean'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ),
        new Post(
            uriTemplate: '/medias',
            controller: 'App\Controller\MediasController::add_medias',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Add a new media',
                    'description' => 'Adds a new media to the collection.',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => ['type' => 'string'],
                                        'author' => ['type' => 'string'],
                                        'type' => ['type' => 'string'],
                                        'description' => ['type' => 'string'],
                                        'image' => ['type' => 'string'],
                                        'status' => ['type' => 'boolean'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Media created successfully',
                        ],
                    ],
                ],
            ]
        ),
        new Delete(
            uriTemplate: '/medias/{idMedia}',
            controller: 'App\Controller\MediasController::delete_media',
            extraProperties: [
                'openapi_context' => [
                    'summary' => 'Delete a media',
                    'description' => 'Deletes a specific media from the collection.',
                    'responses' => [
                        '204' => [
                            'description' => 'Media deleted successfully',
                        ],
                    ],
                ],
            ]
        ),
    ]
)]
class MediaResource
{
    // classe vide juste pour mettre dans la doc les methodes
}