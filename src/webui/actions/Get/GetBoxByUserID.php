<?php

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetBoxByUserID {

    public function __invoke(Request $request, Response $response, array $args): Response {

        $view = Twig::fromRequest($request);

        // ⚠️ À remplacer par l'ID de l'utilisateur connecté
        $userId = '9c02505f-af68-4b51-a5b6-e52b1805eee1';

        $mesBoxes = Box::where('createur_id', $userId)->get();

        return $view->render($response, 'mesBox.twig', [
            'boxes' => $mesBoxes
        ]);
    }
}
