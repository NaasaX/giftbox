<?php
declare(strict_types=1);

namespace gift\actions;

use gift\models\Prestation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPrestationAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $params = $request->getQueryParams();
            $id = $params['id'] ?? null;

            if (!$id) {
                throw new \InvalidArgumentException("Erreur : paramètre 'id' manquant dans l'URL.");
            }

            $prestation = Prestation::where('id', $id)->first();
            if (!$prestation) {
                throw new \RuntimeException("Erreur : prestation introuvable.");
            }

            $html = <<<HTML
                <h1>{$prestation->libelle}</h1>
                <p>{$prestation->description}</p>
                <p>Tarif : {$prestation->tarif} €</p>
            HTML;

            $response->getBody()->write($html);
            return $response;
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        } catch (\RuntimeException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write("Erreur interne du serveur.");
            return $response->withStatus(500);
        }
    }
}