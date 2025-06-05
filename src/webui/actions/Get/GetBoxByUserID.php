<?php

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Repository\UserRepository;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetBoxByUserID {

    public function __invoke(Request $request, Response $response, array $args): Response {

        $view = Twig::fromRequest($request);
        $userRepository = new UserRepository();
        $session = new SessionAuthProvider($userRepository);

        $userId = $session->getCurrentUserId();

        if (!$userId) {
            // Redirection vers login ou message d'erreur
            return $response->withHeader('Location', '/signin')->withStatus(302);
        }

        $mesBoxes = Box::where('createur_id', $userId)->get();

        return $view->render($response, 'mesBox.twig', [
            'boxes' => $mesBoxes
        ]);
    }
}
