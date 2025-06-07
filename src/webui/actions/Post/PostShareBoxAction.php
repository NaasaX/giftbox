<?php

namespace Giftbox\webui\actions\Post;

use Giftbox\ApplicationCore\Application\UseCases\BoxShareServiceInterface;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShareBoxAction
{
    private SessionAuthProvider $authProvider;
    private BoxShareServiceInterface $shareService;

    public function __construct(SessionAuthProvider $authProvider, BoxShareServiceInterface $shareService)
    {
        $this->authProvider = $authProvider;
        $this->shareService = $shareService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $boxId = $args['id'] ?? null;
        if (!$boxId) {
            $response->getBody()->write("Box ID manquant");
            return $response->withStatus(400);
        }

        $userId = $this->authProvider->getCurrentUserId();
        if (!$userId) {
            // Pas authentifié, accès refusé
            $response->getBody()->write("Accès refusé : utilisateur non authentifié");
            return $response->withStatus(401);
        }

        $userRole = $this->authProvider->getUserRole();

        try {

            $box = $this->shareService->shareBox($boxId, $userId, $userRole);

            // Si la box n'est pas dans un état "validée" (statut == 2), on refuse
            if ($box->statut == 2) {
                $_SESSION['flash_message'] = "La box ne peut être partagée que si elle est validée.";
                return $response->withHeader('Location', '/box/' . $boxId)->withStatus(302);
            }

            $_SESSION['flash_message'] = "Box partagée avec succès !";
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = "Erreur lors du partage : " . $e->getMessage();
            return $response->withHeader('Location', '/box/' . $boxId)->withStatus(302);
        }

        return $response->withHeader('Location', '/box/' . $boxId)->withStatus(302);
    }
}
