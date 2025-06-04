<?php
namespace Giftbox\webui\actions\Post;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class PostAjoutPanierAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {

        $data = $request->getParsedBody();
        $prestationId = $data['prestation_id'] ?? null;
        $boxId = $_SESSION['box_id'] ?? null;

        // Vérification des données
        if (!$boxId || !$prestationId) {
            $response->getBody()->write('Box ou prestation manquante.');
            return $response->withStatus(400);
        }

        $box = Box::find($boxId);
        $prestation = Prestation::find($prestationId);

        if (!$box || !$prestation) {
            $response->getBody()->write('Box ou prestation introuvable.');
            return $response->withStatus(404);
        }

        if ($box->statut !== 1) {
            $response->getBody()->write('La box n\'est plus modifiable.');
            return $response->withStatus(403);
        }

        // Ajout ou incrémentation dans la base via la relation many-to-many
        $existing = $box->prestations()->where('presta_id', $prestationId)->first();

        if ($existing) {
            $quantiteActuelle = $existing->pivot->quantite;
            $box->prestations()->updateExistingPivot($prestationId, [
                'quantite' => $quantiteActuelle + 1
            ]);
        } else {
            $box->prestations()->attach($prestationId, ['quantite' => 1]);
        }

        // Redirection
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('box', ['id' => $boxId]);

        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
