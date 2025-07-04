<?php
declare(strict_types=1);

namespace giftbox\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Giftbox\ApplicationCore\Application\UseCases\AuthnServiceInterface;
use Giftbox\ApplicationCore\Application\UseCases\AuthnService;
use Giftbox\ApplicationCore\Domain\Repository\UserRepository;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Slim\Routing\RouteContext;

class SigninAction
{
    private AuthnServiceInterface $authnService;
    private SessionAuthProvider $authProvider;

    public function __construct()
    {
        $userRepository = new UserRepository();
        
        // Pass the userRepository to SessionAuthProvider constructor
        $this->authProvider = new SessionAuthProvider($userRepository);
        
        $this->authnService = new AuthnService($userRepository, $this->authProvider);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        // If already signed in, redirect to home
        // Note: SessionAuthProvider doesn't have isSignedIn() method, use isAuthenticated() instead
        if ($this->authProvider->isAuthenticated()) {
            $url = $routeParser->urlFor('home'); // Assuming 'home' is the name of your home route
            return $response->withHeader('Location', $url)->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';

            $user = $this->authnService->verifyCredentials($email, $password);
            
            if ($user) {
                $url = $routeParser->fullUrlFor($request->getUri(), 'home');        
                
                return $response->withHeader('Location', $url)->withStatus(302);
                                error_log('crash');

            } else {
                // Signin failed
                $view = Twig::fromRequest($request);
                return $view->render($response, 'signin.twig', ['error' => 'Invalid email or password.']);
            }
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'signin.twig');
    }
}