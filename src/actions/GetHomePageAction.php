<?php
declare(strict_types=1);

namespace gift\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use gift\models\Coffret_type;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetHomePageAction {
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        // $html = <<<HTML
        // <!DOCTYPE html>
        // <html lang="fr">
        // <head>
        //     <meta charset="UTF-8">
        //     <title>Accueil - MyGiftBox</title>
        // </head>
        // <body>
        //     <header>
        //         <nav>
        //             <a href="/coffrets">Voir les coffrets</a>
        //         </nav>
        //     </header>

        //     <main>
        //         <h1>Bienvenue sur l'application</h1>
        //         <p>MyGiftBox.net est une application web qui permet de choisir, acheter 
        //         et éventuellement offrir des coffrets-cadeaux construits sur mesure.</p>
        //         <p>Un coffret cadeau est formé d'un ensemble de prestations : activités sportives,
        //         activités culturelles, gastronomie, hébergement, visites, attentions particulières, etc...</p>
        //     </main>
        // </body>
        // </html>
        // HTML;

        $twig = Twig::fromRequest($request);
            return $twig->render($response, 'index.twig', [
                'prestation' => $prestation
            ]);

        $response->getBody()->write($html);
        return $response;
    }
}
