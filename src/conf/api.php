<?php

use Giftbox\Api\Get\GetCategoriesApiAction;
use Giftbox\Api\Get\GetBoxApiAction;
use Giftbox\Api\Get\GetPrestationsApiAction;
use Giftbox\Api\Get\GetPrestationsByCategorieApiAction;

// Route pour la liste des catégories
$app->get('/api/categories', GetCategoriesApiAction::class);

// Route pour le détail d’un coffret
$app->get('/api/box/{id}', GetBoxApiAction::class);

// Route pour la liste des prestations
$app->get('/api/prestations', GetPrestationsApiAction::class);

// Route pour la liste des prestations par catégorie
$app->get('/api/categories/{id}/prestations', GetPrestationsByCategorieApiAction::class);

