<?php
declare(strict_types=1);

use Slim\App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Giftbox\WebUI\Actions\GetHomePageAction;
use Giftbox\WebUI\Actions\GetCategoriesAction;
use Giftbox\WebUI\Actions\GetCategorieAction;
use Giftbox\WebUI\Actions\GetPrestationAction;
use Giftbox\WebUI\Actions\GetCoffretsAction;
use Giftbox\WebUI\Actions\GetCoffretAction;
use Giftbox\WebUI\Actions\SigninAction;
use Giftbox\WebUI\Actions\SignoutAction;
use Giftbox\WebUI\Actions\SignupAction;

return function(App $app): App {

  // Route 0 : GET /
  $app->get('/', GetHomePageAction::class)->setName('home');

  // Route 1 : GET /categories
  $app->get('/categories', GetCategoriesAction::class);

  // Route 2 : GET /categories/{id}
  $app->get('/categories/{id}', GetCategorieAction::class);

  // Route 3 : GET /prestation?id=xxxx
  $app->get('/prestation', GetPrestationAction::class);

  // Route 4 : GET /coffrets
  $app->get('/coffrets', GetCoffretsAction::class);

  $app->get('/coffrets/{id}', GetCoffretAction::class);

  // Route 4 : GET /categories/{cat_id}/prestations/{id_prestation}
    $app->get('/categories/{cat_id}/prestations/{presta_id}', function ($request, $response, $args) {
        $cat_id = $args['cat_id'];
        $presta_id = $args['presta_id'];
    });

  // Route 5 : Signin

  $app->get('/signin', SigninAction::class)->setName('signin');
  $app->post('/signin', SigninAction::class)->setName('signin_post');

  // Route for Signout
  $app->get('/signout', SignoutAction::class)->setName('signout');

  // Route for Signup
  $app->get('/signup', SignupAction::class)->setName('signup');
  $app->post('/signup', SignupAction::class)->setName('signup_post');

    return $app;
};
