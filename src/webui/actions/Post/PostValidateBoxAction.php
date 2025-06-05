<?php

namespace Giftbox\webui\actions\Post;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Giftbox\ApplicationCore\Domain\Entities\Box;

class PostValidateBoxAction {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $boxId = $_SESSION['current_box_id'] ?? null;
        if (!$boxId) {
            $response->getBody()->write("Aucune box en cours.");
            return $response->withStatus(400);
        }

        $box = Box::find($boxId);
        if (!$box) {
            $response->getBody()->write("Box introuvable.");
            return $response->withStatus(404);
        }

        // Simule validation : on pourrait par exemple ajouter une colonne "is_validated"
        $box->validated = true; // colonne booléenne à prévoir dans la BDD
        $box->save();

        unset($_SESSION['current_box_id']);

        $response->getBody()->write("Coffret validé avec succès !");
        return $response;
    }
}
