<?php
declare(strict_types=1);

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;


class GetCategoriesAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $categories = Categorie::all();

            // Rendu avec Twig
            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'list_categorie.twig', [
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue : " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
