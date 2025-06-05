<?php

namespace Giftbox\webui\actions\Post;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;

class PostValidateBoxAction {

    private SessionAuthProvider $authProvider;

    public function __construct(SessionAuthProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

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

        $userRole = $this->authProvider->getUserRole();
        $userId = $this->authProvider->getCurrentUserId();

        if ($userRole === null || $userRole < 1 || $box->createur_id !== $userId) {
            throw new HttpForbiddenException($request, "Vous n'avez pas les droits nécessaires pour valider cette box.");
        }

        // Vérifier qu'il y a au moins 2 prestations associées
        $prestationCount = $box->prestations()->count();
        if ($prestationCount < 2) {
            $_SESSION['flash_message'] = "Vous devez ajouter au moins deux prestations pour valider ce coffret.";
            return $response
                ->withHeader('Location', '/box/' . $boxId)
                ->withStatus(302);
        }

        $box->statut = 2;
        $box->save();

        unset($_SESSION['current_box_id']);
        $_SESSION['flash_message'] = "Coffret validé avec succès !";

        return $response
            ->withHeader('Location', '/box/' . $boxId)
            ->withStatus(302);

    }
}
