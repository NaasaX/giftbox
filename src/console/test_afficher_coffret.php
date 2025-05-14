<?php

require_once __DIR__ . '/../vendor/autoload.php';
var_dump(class_exists('gift\appli\models\Prestation'));
require_once __DIR__ . '/../models/Prestation.php';
require_once __DIR__ . '/../models/Coffret_type.php';
require_once __DIR__ . '/../conf/database.php';

use gift\appli\models\Coffret_type;

$liste = Coffret_type::all();

if (!empty($liste)) {
    foreach ($liste as $coffret) {
        echo $coffret->libelle . "\n";

        // Affichez les prestations associées à chaque coffret
        $prestations = $coffret->prestations()->get();
        if (!empty($prestations)) {
            foreach ($prestations as $presta) {
                echo " - " . $presta->libelle . "\n";
            }
        } else {
            echo "No prestations found for this coffret.\n";
        }
    }
} else {
    echo "No coffrets found.\n";
}

