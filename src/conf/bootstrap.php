<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;
use Giftbox\Infrastructure\Eloquent;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Giftbox\ApplicationCore\Domain\Repository\UserRepository;
use Giftbox\ApplicationCore\Application\UseCases\AuthnService;
use Giftbox\Webui\Providers\SessionAuthProvider;

require_once __DIR__ . '/../../vendor/autoload.php';

// Initialiser Eloquent
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

// Créer l'application
$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../webui/views', ['cache' => false]);

// Instantiate AuthProvider and add it to Twig globals
$userRepository = new UserRepository();
// SessionAuthProvider now only needs UserRepositoryInterface
$authProvider = new SessionAuthProvider($userRepository);
// AuthnService needs UserRepositoryInterface and AuthProviderInterface
$authnService = new AuthnService($userRepository, $authProvider);
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
$app->add(new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    true, // afficher erreurs
    true, // logger erreurs
    true  // logger détails
));

// Charger les routes
$app = (require_once __DIR__ . '/routes.php')($app);

// Retourner l’application configurée
return $app;
