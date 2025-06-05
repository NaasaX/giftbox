<?php
declare(strict_types=1);

use Giftbox\webui\actions\Get\GetBoxByUserID;
use Giftbox\webui\actions\Get\GetCategorieAction;
use Giftbox\webui\actions\Get\GetCategoriesAction;
use Giftbox\webui\actions\Get\GetCoffretAction;
use Giftbox\webui\actions\Get\GetCoffretsAction;
use Giftbox\webui\actions\Get\GetCreateCustomBoxAction;
use Giftbox\webui\actions\Get\GetCreateNewBoxAction;
use Giftbox\webui\actions\Get\GetHomePageAction;
use Giftbox\webui\actions\Get\GetPrestationAction;
use Giftbox\webui\actions\Get\GetBoxDetails;
use Giftbox\webui\actions\Post\PostSaveCustomBoxAction;
use Slim\App;
use Giftbox\webui\actions\Get\GetCatalogueAction;
use Giftbox\webui\actions\Post\PostAjoutPanierAction;
use Giftbox\WebUI\Actions\SigninAction;
use Giftbox\WebUI\Actions\SignoutAction;
use Giftbox\WebUI\Actions\SignupAction;
use Giftbox\webui\actions\Get\GetCurrentBoxAction;
use Giftbox\webui\actions\Post\PostValidateBoxAction;

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

  $app->get('/creer-box', GetCreateNewBoxAction::class);

  $app->get('/creer-custom-box', GetCreateCustomBoxAction::class);

  $app->get('/mes-box', GetBoxByUserID::class);

  $app->get('/box/{id}', GetBoxDetails::class)->setName('box');

  $app->post('/sauver-box', PostSaveCustomBoxAction::class);

    // Route 4 : GET /categories/{cat_id}/prestations/{id_prestation}
    $app->get('/categories/{cat_id}/prestations/{presta_id}', function ($request, $response, $args) {
        $cat_id = $args['cat_id'];
        $presta_id = $args['presta_id'];
    });

  // Route 5 : GET /catalogue
  $app->get('/catalogue', GetCatalogueAction::class)->setName('catalogue');

  $app->post('/panier/ajouter', PostAjoutPanierAction::class)->setName('ajout_panier');

    // Route 5 : Signin

  $app->get('/signin', SigninAction::class)->setName('signin');
  $app->post('/signin', SigninAction::class)->setName('signin_post');

  // Route for Signout
  $app->get('/signout', SignoutAction::class)->setName('signout');

  // Route for Signup
  $app->get('/signup', SignupAction::class)->setName('signup');
  $app->post('/signup', SignupAction::class)->setName('signup_post');

  // Route validation de la box
  $app->post('/box/valider', PostValidateBoxAction::class);


    return $app;
};