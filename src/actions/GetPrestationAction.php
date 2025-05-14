<?php
declare(strict_types=1);

namespace gift\actions;

use gift\models\Prestation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpInternalServerErrorException;

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
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (\RuntimeException $e) {
            throw new HttpInternalServerErrorException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new HttpException($request, $e->getMessage());
        }
    }
}