<?php
declare(strict_types=1);

namespace gift\actions;

use gift\models\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCategoriesAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $categories = Categorie::all();

            $html = <<<HTML
                <h1>Liste des catégories</h1>
                <ul>
            HTML;

            foreach ($categories as $cat) {
                $url = "/categorie/{$cat->id}";
                $html .= <<<HTML
                    <li>
                        [{$cat->id}] <a href="$url">{$cat->libelle}</a>
                    </li>
                HTML;
            }

            $html .= <<<HTML
                </ul>
            HTML;

            $response->getBody()->write($html);
        } catch (\Exception $e) {
            $response->getBody()->write('<h1>Une erreur est survenue lors de la récupération des catégories.</h1>');
            $response = $response->withStatus(500);
        }

        return $response;
    }
}
