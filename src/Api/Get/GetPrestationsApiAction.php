<?php

namespace Giftbox\Api\Get;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;

class GetPrestationsApiAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $prestations = Prestation::all();

        $data = [
            'type' => 'collection',
            'count' => $prestations->count(),
            'prestations' => []
        ];

        foreach ($prestations as $presta) {
            $data['prestations'][] = [
                'id'          => $presta->id,
                'libelle'     => $presta->libelle,
                'description' => $presta->description,
                'tarif'       => $presta->tarif,
                'cat_id'=> $presta->cat_id,
            ];
        }

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}