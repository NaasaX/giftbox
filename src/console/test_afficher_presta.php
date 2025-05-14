<?php

require_once __DIR__ . '/../vendor/autoload.php';
var_dump(class_exists('gift\appli\models\Prestation'));

require_once __DIR__ . '/../models/Prestation.php';


require_once __DIR__ . '/../conf/database.php';

use gift\appli\models\Prestation;

try {
  $prestaId = '4cca8b8e-0244-499b-8247-d217a4bc542d';
  $presta = Prestation::findOrFail($prestaId);

  echo "Prestation Details:\n";
  echo "ID: " . $presta->id . "\n";
  echo "Name: " . $presta->libelle . "\n";
  echo "Description: " . $presta->description . "\n";
  echo "Price: " . $presta->tarif . "\n";
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
  echo "Error: No prestation found with the provided ID.\n";
}

