<?php

require_once __DIR__ . '/../vendor/autoload.php';
var_dump(class_exists('gift\appli\models\Prestation'));

require_once __DIR__ . '/../models/Prestation.php';


require_once __DIR__ . '/../conf/database.php';

use gift\appli\models\Prestation;


$prestations = Prestation::all();

foreach ($prestations as $presta) {
    echo $presta->libelle . ' - ' . $presta->description . ' - '. $presta->tarif. ' - ' . $presta->unite . "\n";
}
