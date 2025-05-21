<?php
declare(strict_types=1);

namespace gift\actions;

use gift\models\Coffret_type;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class GetCoffretAction {

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id = $args['id'] ?? null;

            if (!$id) {
                throw new \InvalidArgumentException("Erreur : paramètre 'id' manquant dans l'URL.");
            }

            $coffret = Coffret_type::find($id);

            if (!$coffret) {
                $response->getBody()->write("Coffret non trouvé.");
                return $response->withStatus(404);
            }

            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'coffret.twig', [
                'coffret' => $coffret,
                'prestations' => $coffret->prestations,
            ]);
        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());

        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue : " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}