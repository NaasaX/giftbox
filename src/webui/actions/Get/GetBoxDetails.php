<?php

declare(strict_types=1);

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Entities\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetBoxDetails {

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);

        $boxId = $args['id'] ?? null;

        if (!$boxId) {
            // Gérer le cas où l'id n'est pas fourni
            $response->getBody()->write("Box ID manquant");
            return $response->withStatus(400);
        }

        $box = Box::with('prestations')->find($boxId);

        if (!$box) {
            $response->getBody()->write("Box non trouvée");
            return $response->withStatus(404);
        }

        $_SESSION['current_box_id'] = $box->id;

        $montantTotal = 0.0;
        foreach ($box->prestations as $presta) {
            $montantTotal += $presta->tarif * $presta->pivot->quantite;
        }
        $box->montant = $montantTotal;

        $_SESSION['current_box_id'] = $box->id;

        $categories = Categorie::with('prestations')->get();


        $flashMessage = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_message']);

        // Passer la box et ses prestations au template Twig
        return $view->render($response,'boxDetails.twig', [
            'box' => $box,
            'categories' => $box->statut === 1 ? $categories : [],
            'flash_message' => $flashMessage
        ]);
    }

}
