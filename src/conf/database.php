<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$iniPath = __DIR__ . '/gift.db.conf.ini';

if (!file_exists($iniPath)) {
    die("Erreur : le fichier gift.db.ini est introuvable à $iniPath\n");
}

$ini = parse_ini_file($iniPath, true);

if (!isset($ini['database'])) {
    die("Erreur : la section [database] est absente dans le fichier INI.\n");
}

$config = $ini['database'];

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => $config['driver'],
    'host'      => $config['host'],
    'database'  => $config['database'],
    'username'  => $config['username'],
    'password'  => $config['password'],
    'charset'   => $config['charset'],
    'collation' => $config['collation'],
    'prefix'    => $config['prefix'],
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
