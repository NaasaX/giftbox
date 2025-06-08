<?php
declare(strict_types=1);

use Giftbox\ApplicationCore\Application\UseCases\BoxByTokenService;
use Giftbox\ApplicationCore\Application\UseCases\BoxByTokenServiceInterface;
use Giftbox\ApplicationCore\Application\UseCases\BoxCreationService;
use Giftbox\ApplicationCore\Application\UseCases\BoxCreationServiceInterface;
use Giftbox\ApplicationCore\Application\UseCases\BoxShareService;
use Giftbox\ApplicationCore\Application\UseCases\BoxShareServiceInterface;
use Giftbox\ApplicationCore\Application\UseCases\BoxValidationService;
use Giftbox\ApplicationCore\Application\UseCases\BoxValidationServiceInterface;
use Giftbox\ApplicationCore\Application\UseCases\GetBoxDetailsInterface;
use Giftbox\ApplicationCore\Application\UseCases\GetBoxDetailsService;
use Giftbox\ApplicationCore\Application\UseCases\PanierService;
use Giftbox\ApplicationCore\Application\UseCases\PanierServiceInterface;
use Giftbox\ApplicationCore\Domain\Repository\BoxRepository;
use Giftbox\ApplicationCore\Domain\Repository\BoxRepositoryInterface;
use Giftbox\ApplicationCore\Domain\Repository\UserRepositoryInterface;
use Slim\Factory\AppFactory;
use Giftbox\Infrastructure\Eloquent;
use Slim\Middleware\ErrorMiddleware;
use Slim\Exception\HttpForbiddenException;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Giftbox\ApplicationCore\Domain\Repository\UserRepository;
use Giftbox\ApplicationCore\Application\UseCases\AuthnService;
use Giftbox\Webui\Providers\SessionAuthProvider;
use DI\Container;

require_once __DIR__ . '/../../vendor/autoload.php';

// Initialiser Eloquent
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

// Créer l'application
$container = new Container();
AppFactory::setContainer($container);

$container->set(BoxRepository::class, function () {
    return new BoxRepository(); // à condition que cette classe existe vraiment
});

$container->set(BoxCreationServiceInterface::class, function ($c) {
    return new BoxCreationService($c->get(BoxRepository::class));
});

$container->set(BoxRepositoryInterface::class, function() {
    return new BoxRepository();
});

$container->set(GetBoxDetailsInterface::class, function($c) {
    return new GetBoxDetailsService(
        $c->get(BoxRepositoryInterface::class)
    );
});

$container->set(PanierServiceInterface::class, function() {
    return new PanierService();
});

$container->set(UserRepositoryInterface::class, function(){
    return new UserRepository();
});

$container->set(BoxValidationServiceInterface::class, function (){
    return new BoxValidationService();
});

$container->set(BoxByTokenServiceInterface::class, function (){
    return new BoxByTokenService();
});

$container->set(BoxShareServiceInterface::class, function (){
    return new BoxShareService();
});

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../webui/views', ['cache' => false]);

// Instantiate AuthProvider and add it to Twig globals
$userRepository = new UserRepository();
// SessionAuthProvider now only needs UserRepositoryInterface
$authProvider = new SessionAuthProvider($userRepository);
// AuthnService needs UserRepositoryInterface and AuthProviderInterface
$authnService = new AuthnService($userRepository, $authProvider);

$getBoxDetailsService = $container->get(GetBoxDetailsInterface::class);

$panierService = $container->get(PanierServiceInterface::class);

$boxValidation = $container->get(BoxValidationServiceInterface::class);

$twig->getEnvironment()->addGlobal('auth', $authProvider);

$twig->getEnvironment()->addGlobal('assets_css', '/css');
$twig->getEnvironment()->addGlobal('assets_img', '/img');
$twig->getEnvironment()->addGlobal('nav_items', [
    ['url' => '/', 'label' => 'Accueil'],
    ['url' => '/categories', 'label' => 'Liste des catégories']
]);

$app->add(TwigMiddleware::create($app, $twig));

// Ajouter le middleware de routing
$app->addRoutingMiddleware();

// Ajouter le middleware d’erreur
$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    true, // afficher erreurs
    true, // logger erreurs
    true  // logger détails
);

// Custom error handler for HttpForbiddenException
$errorMiddleware->setErrorHandler(HttpForbiddenException::class, function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $view = Twig::fromRequest($request);
    return $view->render($response->withStatus(403), 'error.twig', [
        'error_message' => $exception->getMessage()
    ]);
});

$app->add($errorMiddleware);

// Charger les routes
$routes = require_once __DIR__ . '/routes.php';
$app = $routes($app, $authProvider, $getBoxDetailsService, $panierService, $boxValidation); // Pass $authProvider to the routes closure

require_once __DIR__ . '/api.php';

// Retourner l’application configurée
return $app;
