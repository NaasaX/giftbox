<?php

use Giftbox\Api\Get\GetCategoriesApiAction;
use Giftbox\Api\Get\GetBoxApiAction;

// Route pour la liste des catégories
$app->get('/api/categories', GetCategoriesApiAction::class);

// Route pour le détail d’un coffret
$app->get('/api/box/{id}', GetBoxApiAction::class);