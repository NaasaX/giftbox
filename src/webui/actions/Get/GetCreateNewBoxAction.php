<?php

namespace Giftbox\webui\actions\Get;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class GetCreateNewBoxAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
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