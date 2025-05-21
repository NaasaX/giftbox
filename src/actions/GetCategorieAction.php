<?php
declare(strict_types=1);

namespace gift\actions;

use gift\models\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class GetCategorieAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id = $args['id'] ?? null;

            if (!$id) {
                throw new \InvalidArgumentException("Erreur : paramètre 'id' manquant dans l'URL.");
            }

            $categorie = Categorie::find($id);

            if (!$categorie) {
                $response->getBody()->write("Catégorie non trouvée.");
                return $response->withStatus(404);
            }

            // Rendu avec Twig
            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'categorie.twig', [
                'categories' => $categorie,
                'prestations' => $categorie->prestations,
            ]);

        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());

        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue : " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
