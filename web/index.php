<?php

require_once(__DIR__ . '/web_autoload.php'); // автозагрузка классов

$localConfig = require(__DIR__ . '/../application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/../application/config/web.php'), 
    $localConfig);

require_once(__DIR__ . '/../application/bootstrap.php');


\ItForFree\SimpleMVC\Application::get()
    ->setConfiguration($config)
    ->run();
