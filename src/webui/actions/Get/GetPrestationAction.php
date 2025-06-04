<?php
declare(strict_types=1);

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Prestation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Views\Twig;

class GetPrestationAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $params = $request->getQueryParams();
            $id = $params['id'] ?? null;

            if (!$id) {
                throw new \InvalidArgumentException("Erreur : paramètre 'id' manquant dans l'URL.");
            }

            $prestation = Prestation::with('categorie')->find($id);
            if (!$prestation) {
                throw new \RuntimeException("Erreur : prestation introuvable.");
            }

            // Rendu avec Twig
            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'prestation.twig', [
                'prestation' => $prestation
            ]);

        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (\RuntimeException $e) {
            throw new HttpInternalServerErrorException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new HttpException($request, $e->getMessage());
        }
    }
}
