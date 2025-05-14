<?php
declare(strict_types=1);

namespace gift\actions;

use gift\models\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetCategorieAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $categorie = Categorie::find($args['id']);

            if (!$categorie) {
                //$response->getBody()->write("Catégorie non trouvée.");
                //return $response->withStatus(404);
                throw new \InvalidArgumentException("Erreur : paramètre 'id' manquant dans l'URL.");
            }

            $html = <<<HTML
                <h1>Catégorie : {$categorie->libelle}</h1>
                <p>{$categorie->description}</p>
            HTML;

            $response->getBody()->write($html);
            return $response;
        } catch (\InvalidArgumentException $e) {

            throw new HttpBadRequestException($request, $e->getMessage());

        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue : " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
