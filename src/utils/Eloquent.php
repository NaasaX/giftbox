<?php
namespace Giftbox\Utils;

use Illuminate\Database\Capsule\Manager as Capsule;

class Eloquent
{
    public static function init(string $configFile): void
    {
        $config = parse_ini_file($configFile, true);
        
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => $config['database']['driver'],
            'host' => $config['database']['host'],
            'database' => $config['database']['database'],
            'username' => $config['database']['username'],
            'password' => $config['database']['password'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
