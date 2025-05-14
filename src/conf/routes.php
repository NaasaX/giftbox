<?php
declare(strict_types=1);

use Slim\App;
use gift\actions\GetCategoriesAction;
use gift\actions\GetCategorieAction;
use gift\actions\GetPrestationAction;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

return function(App $app): App {

  // Route 0 : GET /
  $app->get('/', function(Request $request, Response $response) {
      $response->getBody()->write("Bienvenue sur l'application");
      return $response;
  }); 

  // Route 1 : GET /categories
  $app->get('/categories', GetCategoriesAction::class);

  // Route 2 : GET /categories/{id}
  $app->get('/categories/{id}', GetCategorieAction::class);

  // Route 3 : GET /prestation?id=xxxx
  $app->get('/prestation', GetPrestationAction::class);

  return $app;
};
