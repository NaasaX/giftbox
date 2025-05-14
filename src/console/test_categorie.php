<?php

require_once __DIR__ . '/../vendor/autoload.php';
var_dump(class_exists('gift\appli\models\Categorie'));

require_once __DIR__ . '/../models/Categorie.php';


require_once __DIR__ . '/../conf/database.php';

use gift\appli\models\Categorie;


$categories = Categorie::all();

foreach ($categories as $cat) {
    echo $cat->id . ' - ' . $cat->libelle . "\n";
}
