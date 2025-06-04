<?php
namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetCatalogueAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $categories = Categorie::with('prestations')->get();

        $twig = Twig::fromRequest($request);
        return $twig->render($response, 'catalogue.twig', [
            'categories' => $categories,
        ]);
    }
}
