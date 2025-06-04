<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/conf/bootstrap.php';


session_start();

/** @var \Slim\App $app */
$app->run();