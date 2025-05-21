<?php
declare(strict_types=1);

namespace gift\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetHomePageAction {
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Accueil - MyGiftBox</title>
        </head>
        <body>
            <header>
                <nav>
                    <a href="/coffrets">Voir les coffrets</a>
                </nav>
            </header>

            <main>
                <h1>Bienvenue sur l'application</h1>
                <p>MyGiftBox.net est une application web qui permet de choisir, acheter 
                et éventuellement offrir des coffrets-cadeaux construits sur mesure.</p>
                <p>Un coffret cadeau est formé d'un ensemble de prestations : activités sportives,
                activités culturelles, gastronomie, hébergement, visites, attentions particulières, etc...</p>
            </main>
        </body>
        </html>
        HTML;

        $response->getBody()->write($html);
        return $response;
    }
}
