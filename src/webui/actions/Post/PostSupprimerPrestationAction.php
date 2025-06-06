<?php

namespace Giftbox\webui\actions\Post;

use Giftbox\ApplicationCore\Application\UseCases\PanierServiceInterface;
use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\Webui\providers\CsrfTokenProvider;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;

class PostSupprimerPrestationAction
{
    private SessionAuthProvider $authProvider;
    private PanierServiceInterface $panierService;

    public function __construct(SessionAuthProvider $authProvider, PanierServiceInterface $panierService)
    {
        $this->authProvider = $authProvider;
        $this->panierService = $panierService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {

        $data = $request->getParsedBody();
        try {
            $token = $data['csrf_token'] ?? '';
            CsrfTokenProvider::check($token);
        } catch (\Exception $e) {
            $response->getBody()->write("Erreur CSRF : " . $e->getMessage());
            return $response->withStatus(403);
        }

        $boxId = $args['id'];
        $prestationId = $args['prestation_id'];

        $box = Box::find($boxId);
        if (!$box) {
            $response->getBody()->write('Box introuvable.');
            return $response->withStatus(404);
        }

        $userRole = $this->authProvider->getUserRole();
        $userId = $this->authProvider->getCurrentUserId();

        if ($userRole === null || $userRole < 1 || $box->createur_id !== $userId) {
            throw new HttpForbiddenException($request, "Vous n'avez pas les droits nécessaires.");
        }

        if ($box->statut !== 1) {
            $response->getBody()->write('La box n\'est plus modifiable.');
            return $response->withStatus(403);
        }

        try {
            $this->panierService->supprimerPrestation($boxId, $prestationId);
            $_SESSION['flash_message'] = "Prestation supprimée du coffret temporaire.";
        } catch (\InvalidArgumentException $e) {
            $_SESSION['flash_message'] = $e->getMessage();
        }

        return $response
            ->withHeader('Location', '/box/' . $boxId)
            ->withStatus(302);
    }
}
