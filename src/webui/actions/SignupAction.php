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

class SignupAction
{
    private AuthnServiceInterface $authnService;

    public function __construct()
    {
        $userRepository = new UserRepository();
        
        // Pass the userRepository to SessionAuthProvider constructor
        $authProvider = new SessionAuthProvider($userRepository);
        
        $this->authnService = new AuthnService($userRepository, $authProvider);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';
            $confirmPassword = $params['confirm_password'] ?? '';

            // Basic validation
            if (empty($email) || empty($password) || empty($confirmPassword)) {
                // Handle error: missing fields
                $view = Twig::fromRequest($request);
                return $view->render($response, 'signup.twig', ['error' => 'All fields are required.']);
            }

            if ($password !== $confirmPassword) {
                // Handle error: passwords don't match
                $view = Twig::fromRequest($request);
                return $view->render($response, 'signup.twig', ['error' => 'Passwords do not match.']);
            }

            try {
                $this->authnService->register($email, $password);
                // Redirect to signin page after successful registration
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $url = $routeParser->urlFor('signin');
                return $response->withHeader('Location', $url)->withStatus(302);
            } catch (\Exception $e) {
                // Handle error: registration failed (e.g., email already exists)
                $view = Twig::fromRequest($request);
                return $view->render($response, 'signup.twig', ['error' => $e->getMessage()]);
            }
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'signup.twig');
    }
}