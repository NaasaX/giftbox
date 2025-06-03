<?php

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Prestation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class GetCreateCustomBoxAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $prestations = Prestation::with('categorie')->get();

            // Grouper les prestations par catégorie
            $prestationsParCategorie = [];
            foreach ($prestations as $presta) {
                $cat = $presta->categorie->libelle ?? 'Autres';
                $prestationsParCategorie[$cat][] = $presta;
            }

            return Twig::fromRequest($request)->render($response, 'createCustomBox.twig', [
                'prestationsParCategorie' => $prestationsParCategorie
            ]);

        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());

        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue" . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
