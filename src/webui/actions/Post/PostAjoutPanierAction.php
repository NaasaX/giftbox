<?php
namespace Giftbox\webui\actions\Post;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class PostAjoutPanierAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = $request->getParsedBody();
        $prestationId = $data['prestation_id'] ?? null;

        if (!$prestationId) {
            $response->getBody()->write('Aucune prestation sélectionnée.');
            return $response->withStatus(400);
        }

        $_SESSION['panier'] = $_SESSION['panier'] ?? [];
        if (!in_array($prestationId, $_SESSION['panier'])) {
            $_SESSION['panier'][] = $prestationId;
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response
            ->withHeader('Location', $routeParser->urlFor('catalogue'))
            ->withStatus(302);
    }
}
