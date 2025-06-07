<?php
declare(strict_types=1);

namespace Giftbox\webui\actions\Get;

use Giftbox\ApplicationCore\Domain\Entities\Theme;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class GetCoffretsAction {

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $themes = Theme::with('coffretsTypes')->get();

            if ($themes->isEmpty()) {
                $response->getBody()->write("Aucun thème trouvé");
                return $response->withStatus(404);
            }

            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'coffrets.twig', [
                'themes' => $themes
            ]);

        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());

        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue : " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
