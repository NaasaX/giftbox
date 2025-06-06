<?php

declare(strict_types=1);

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Application\UseCases\GetBoxDetailsInterface;
use Giftbox\Webui\providers\CsrfTokenProvider;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;
use Slim\Views\Twig;

class GetBoxDetails
{
    private SessionAuthProvider $authProvider;
    private GetBoxDetailsInterface $getBoxDetailsService;
    public function __construct(GetBoxDetailsInterface $getBoxDetailsService, SessionAuthProvider $authProvider)
    {
        $this->getBoxDetailsService = $getBoxDetailsService;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        $boxId = $args['id'] ?? null;

        if (!$boxId) {
            $response->getBody()->write("Box ID manquant");
            return $response->withStatus(400);
        }

        $cookieName = "box_{$boxId}_prestations";
        $cookieData = null;

        if (isset($_COOKIE[$cookieName])) {
            $decoded = json_decode($_COOKIE[$cookieName], true);
            if (is_array($decoded)) {
                $cookieData = $decoded;
            }
        }

        try {
            $data = $this->getBoxDetailsService->execute($boxId, $cookieData);
        } catch (\RuntimeException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }

        $box = $data['box'] ?? null;

        $userRole = $this->authProvider->getUserRole();
        $userId = $this->authProvider->getCurrentUserId();

        if ($userRole === null || $userRole < 1 || $box->createur_id !== $userId) {
            throw new HttpForbiddenException($request, "Vous n'avez pas les droits nécessaires pour visualiser cette box.");
        }

        $_SESSION['current_box_id'] = $boxId;

        $flashMessage = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_message']);

        return $view->render($response, 'boxDetails.twig', [
            ...$data,
            'flash_message' => $flashMessage,
            'csrf_token' => CsrfTokenProvider::generate(),
        ]);
    }
}
