<?php

define('ROOT_PATH', __DIR__ . '/../');
require '../Core/SiteEngine.php';
require '../Core/Autoloader.php';
\Core\Autoloader::instance()->addRoot(ROOT_PATH)->init();

$siteEngine = new SiteEngine();
$siteEngine->run();
