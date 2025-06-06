<?php
namespace Giftbox\webui\actions\Post;

use Giftbox\ApplicationCore\Application\UseCases\PanierServiceInterface;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;
use Slim\Routing\RouteContext;

class PostAjoutPanierAction
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
        $prestationId = $data['prestation_id'] ?? null;
        $boxId = $_SESSION['current_box_id'] ?? null;

        if (!$boxId || !$prestationId) {
            $response->getBody()->write('Box ou prestation manquante.');
            return $response->withStatus(400);
        }

        $userRole = $this->authProvider->getUserRole();

        if ($userRole === null || $userRole < 1) {
            throw new HttpForbiddenException($request, "Vous n'avez pas les droits nécessaires.");
        }

        try {
            $this->panierService->ajouterPrestation($boxId, $prestationId);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        } catch (\RuntimeException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(403);
        }

        $_SESSION['flash_message'] = "Prestation ajoutée au coffret (en attente de validation).";

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('box', ['id' => $boxId]);

        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
