<?php

namespace Giftbox\webui\actions\Post;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Gift\ApplicationCore\Services\BoxService;
use Gift\Providers\CsrfTokenProvider;

class PostAddPrestationToBoxAction {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $data = $request->getParsedBody();
        $prestationId = $data['prestation_id'] ?? null;
        $csrfToken = $data['csrf_token'];
        $boxId = $_SESSION['current_box_id'] ?? null;

        // Vérification CSRF
        try {
            CsrfTokenProvider::check($csrfToken);
        } catch (\Exception $e) {
            $response->getBody()->write("Erreur CSRF : " . $e->getMessage());
            return $response->withStatus(403);
        }

        // Vérification des données
        if (!$prestationId || !$boxId) {
            $response->getBody()->write("Erreur : Box ou Prestation manquante");
            return $response->withStatus(400);
        }

        try {
            BoxService::addPrestationToBox($boxId, $prestationId);
        } catch (\Exception $e) {
            $response->getBody()->write("Erreur : " . $e->getMessage());
            return $response->withStatus(500);
        }

        // Redirection vers la page de la box
        return $response
            ->withHeader('Location', "/box/$boxId")
            ->withStatus(302);
    }
}