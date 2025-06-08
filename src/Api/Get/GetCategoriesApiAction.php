<?php

namespace Giftbox\Api\Get;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Giftbox\ApplicationCore\Domain\Entities\Categorie;

class GetCategoriesApiAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $categories = Categorie::all();

        $data = [
            'type' => 'collection',
            'count' => $categories->count(),
            'categories' => []
        ];

        foreach ($categories as $categorie) {
            $data['categories'][] = [
                'categorie' => [
                    'id'          => $categorie->id,
                    'libelle'     => $categorie->libelle,
                    'description' => $categorie->description
                ],
                'links' => [
                    'self' => ['href' => '/categories/' . $categorie->id . '/']
                ]
            ];
        }

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}