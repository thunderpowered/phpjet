<?php

date_default_timezone_set('Europe/Moscow');
$start = microtime(true);

$version = phpversion();

// Checking version should in somewhere in the engine
if ((float)$version < 7.3) {
    echo 'Version required: 7.3, but installed: ' . $version;
    exit();
}

// Defining some directory constants

// Root directory
define('ROOT', __DIR__ . '/../');

// Home directory
define('MVC', ROOT . 'app/mvc/');

// Web directory
define('WEB', ROOT . 'web/');

// Engine directory
define('ENGINE', ROOT . 'app/engine/');

// Images and thumbnails directories (relative paths)
define('IMAGES', 'storage/');
define('THUMBNAILS', 'storage/thumbnails/');

// Engine Core
require_once(ROOT . 'PHPJet.php');
require_once(ROOT . 'app/App.php');

// Load Composer's components
require_once(ROOT . 'vendor/autoload.php');

// Engine version
require_once ENGINE . 'config/version.php';

// Proceed everything
\Jet\PHPJet::init();
$result = \Jet\PHPJet::$app->start();
echo $result;

// Some debug info (temporary)
if (!\Jet\PHPJet::$app->system->request->getPOST()) {
    if (\Jet\App\Engine\Config\Config::$dev['debug']) {
        echo '<!-- Generation time: ' .  (microtime(true) - $start) . ' s. -->';
        echo '<!-- SQL-queries: ' .  \Jet\PHPJet::$app->store->getNumberOfQueries() . ' -->';
    }
    // Just for Fun!
    echo '<!-- ' .  \Jet\PHPJet::$app->system->getEngineVersion() . ' -->';
}