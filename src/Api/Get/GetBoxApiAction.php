<?php

namespace Giftbox\Api\Get;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Giftbox\ApplicationCore\Domain\Repository\BoxRepository;

class GetBoxApiAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $boxRepository = new BoxRepository();
        $box = $boxRepository->findById($id);

        if (!$box) {
            $data = ['error' => 'Box not found'];
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $prestations = [];
        foreach ($box->prestations as $presta) {
            $prestations[] = [
                'libelle'     => $presta->libelle,
                'description' => $presta->description ?? '',
                'contenu'     => [
                    'box_id'    => $box->id,
                    'presta_id' => $presta->id,
                    'quantite'  => $presta->pivot->quantite ?? 1
                ]
            ];
        }

        $data = [
            'type' => 'resource',
            'box' => [
                'id'          => $box->id,
                'libelle'     => $box->libelle,
                'description' => $box->description,
                'message_kdo' => $box->message_kdo,
                'statut'      => $box->statut,
                'prestations' => $prestations
            ]
        ];

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}