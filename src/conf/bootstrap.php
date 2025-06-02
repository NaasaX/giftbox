<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;
use Giftbox\Utils\Eloquent;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../../vendor/autoload.php';

// Initialiser Eloquent
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

// Créer l'application
$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../webui/views', ['cache' => false]);
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
