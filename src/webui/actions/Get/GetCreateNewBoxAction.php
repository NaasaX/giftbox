<?php

namespace Giftbox\webui\actions\Get;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Views\Twig;
use Giftbox\Webui\Providers\SessionAuthProvider;

class GetCreateNewBoxAction
{
    private SessionAuthProvider $authProvider;

    public function __construct(SessionAuthProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $userRole = $this->authProvider->getUserRole();

        if ($userRole === null || $userRole < 1) {
            throw new HttpForbiddenException($request, "Vous n'avez pas les droits nécessaires pour créer une box.");
        }

        try {

            return Twig::fromRequest($request)->render($response, 'createBox.twig');

        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());

        } catch (\Exception $e) {
            $response->getBody()->write("Une erreur est survenue" . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}