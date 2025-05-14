<?php

require_once __DIR__ . '/../vendor/autoload.php';
var_dump(class_exists('gift\appli\models\Prestation'));

require_once __DIR__ . '/../models/Prestation.php';
require_once __DIR__ . '/../conf/database.php';

use gift\appli\models\Prestation;

require_once __DIR__ . '/../models/Categorie.php';

use gift\appli\models\Categorie;


$categorie = Categorie::where('id', 3)->first();


if ($categorie) {
    echo "Categorie 3 : " . $categorie->libelle . "\n";
    echo "Prestations de la categorie 3 : \n";
    $prestations = $categorie->prestations()->get();
    if (!empty($prestations)) {
        foreach ($prestations as $presta) {
            echo $presta->libelle . "\n";
        }
    } else {
        echo "No prestations found for this category.\n";
    }
    
} else {
    echo "Categorie with ID 3 not found.\n";
}