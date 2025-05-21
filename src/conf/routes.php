<?php
declare(strict_types=1);

use gift\actions\GetCoffretAction;
use gift\actions\GetCoffretsAction;
use gift\actions\GetHomePageAction;
use Slim\App;
use gift\actions\GetCategoriesAction;
use gift\actions\GetCategorieAction;
use gift\actions\GetPrestationAction;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

return function(App $app): App {

  // Route 0 : GET /
  $app->get('/', GetHomePageAction::class);

  // Route 1 : GET /categories
  $app->get('/categories', GetCategoriesAction::class);

  // Route 2 : GET /categories/{id}
  $app->get('/categories/{id}', GetCategorieAction::class);

  // Route 3 : GET /prestation?id=xxxx
  $app->get('/prestation', GetPrestationAction::class);

  // Route 4 : GET /coffrets
  $app->get('/coffrets', GetCoffretsAction::class);

  $app->get('/coffrets/{id}', GetCoffretAction::class);

  return $app;
};
