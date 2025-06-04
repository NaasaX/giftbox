<?php
declare(strict_types=1);

namespace giftbox\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Giftbox\ApplicationCore\Application\UseCases\AuthnServiceInterface;
use Giftbox\ApplicationCore\Application\UseCases\AuthnService;
use Giftbox\ApplicationCore\Domain\Repository\UserRepository;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Slim\Routing\RouteContext;

class SignoutAction
{
    private AuthnServiceInterface $authnService;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $authProvider = new SessionAuthProvider($userRepository);
        $this->authnService = new AuthnService($userRepository, $authProvider);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->authnService->signOut(); // This will call authProvider->signOut()

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('home'); // Assuming 'home' is the name of your home route
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}