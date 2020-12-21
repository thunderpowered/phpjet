<?php

/*
 * 
 * ShopEngine
 * todo: create constants inside of one file
 */

$start = microtime(true);

$version = phpversion();

// Checking version should in somewhere in the engine
if ((float)$version < 7.0) {
    echo 'Version required: 7.0, but installed: ' . $version;
    exit();
}

// Defining some directory constants
// Initially it was root.php file with the root namespace
// But i temporary disabled it
// So all constants define here in index.php

// Root directory
define('ROOT', __DIR__ . '/../');

// Home directory
define('MVC', ROOT . 'app/mvc/');

// Web directory
define('WEB', ROOT . 'web/');

// Engine directory
define('ENGINE', ROOT . 'app/engine/');

// Images and thumbnails directories (relative paths)
define('IMAGES', 'uploads/images/');
define('THUMBNAILS', 'uploads/thumbnails/images/');

// Engine Core
require_once(ROOT . 'CloudStore.php');
require_once(ROOT . 'app/App.php');

// Load Composer's components
require_once(ROOT . 'vendor/autoload.php');

// Engine version
require_once ENGINE . 'config/version.php';

// Proceed everything
\CloudStore\CloudStore::init();
$result = \CloudStore\CloudStore::$app->start();
echo $result;

// Debug only
if (\CloudStore\CloudStore::$app->router->getControllerName() !== 'ajax') {
    echo '<span style="display:none" class="debug-info">Generation time: ' . (microtime(true) - $start) . ' s.</span>';
    echo '<span style="display:none" class="debug-info">Number of queries: ' . \CloudStore\CloudStore::$app->store->getNumberOfQueries() . '</span>';
    echo '<span style="display:none" class="debug-info">Engine Version: ' . \CloudStore\CloudStore::$app->system->getEngineVersion() . '</span>';
}