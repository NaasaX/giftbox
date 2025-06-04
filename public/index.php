<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../src/conf/bootstrap.php';

/** @var \Slim\App $app */
$app->run();