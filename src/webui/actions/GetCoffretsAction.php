<?php
declare(strict_types = 1);

namespace gift\webui\actions;

use gift\models\Coffret_type;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetCoffretsAction{

    public function __invoke(Request $request, Response $response, array $args): Response{
        try {

            $coffrets = Coffret_type::all();

            if(!$coffrets){
                $response->getBody()->write("Coffret non trouvé");
                return $response->withStatus(404);
            }

            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'coffrets.twig', [
                'coffret' => $coffrets
            ]);

        } catch (\InvalidArgumentException $e){
            throw new HttpBadRequestException($request, $e->getMessage());

        } catch (\Exception $e){
            $response->getBody()->write("Une erreur est survenu" . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}