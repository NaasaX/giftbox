<?php

namespace Giftbox\webui\actions\Post;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class PostSupprimerPrestationAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $boxId = $args['id'] ?? null;
        $prestationId = $args['prestation_id'] ?? null;

        $box = Box::find($boxId);

        if (!$box || !$prestationId) {
            $response->getBody()->write("Box ou prestation introuvable.");
            return $response->withStatus(404);
        }

        if ($box->statut !== 1) {
            $response->getBody()->write("La box n'est plus modifiable.");
            return $response->withStatus(403);
        }

        $box->prestations()->detach($prestationId);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('box', ['id' => $boxId]);

        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
