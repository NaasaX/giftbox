<?php

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Application\UseCases\BoxByTokenServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use function Symfony\Component\Clock\now;

class GetBoxByTokenAction
{
    private BoxByTokenServiceInterface $boxByTokenService;

    public function __construct(BoxByTokenServiceInterface $boxByTokenService)
    {
        $this->boxByTokenService = $boxByTokenService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $token = $args['token'] ?? null;
        if (!$token) {
            $response->getBody()->write("Token manquant.");
            return $response->withStatus(400);
        }

        try {
            $box = $this->boxByTokenService->getBoxByToken($token);

            if ($box->statut >= 3) {

                $box->updated_at = now();
                $box->statut = 4;
                $box->save();

                $twig = Twig::fromRequest($request);
                return $twig->render($response, 'sharedBoxDetails.twig', [
                    'box' => $box,
                ]);

            }

            $response->getBody()->write("Accès non autorisé à cette box.");
            return $response->withStatus(403);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write("erreur");
            return $response->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue : " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
