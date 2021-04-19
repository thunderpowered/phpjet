<?php
$version = phpversion();
if ((float)$version < 7.3) {
    exit('Version required: 7.3, but installed: ' . $version);
}

date_default_timezone_set('UTC');
$start = microtime(true);

// Root directory
define('ROOT', __DIR__ . '/../');
// Application directory
define('APP', ROOT . 'app/');
// Home directory
define('MVC', ROOT . 'app/mvc/');
// Web directory
define('WEB', ROOT . 'web/');
// Engine directory
define('ENGINE', ROOT . 'app/engine/');

// both deprecated, i'm working on integrating a CDN
// common images are still in the 'common' folder
// todo remove this
// Images
define('IMAGES', 'storage/');
// thumbnails
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
if (!\Jet\PHPJet::$app->system->request->getPOST() && false) {
    if (\Jet\App\Engine\Config\Config::$dev['debug']) {
        echo '<!-- Generation time: ' . (microtime(true) - $start) . ' s. -->';
        echo '<!-- SQL-queries: ' . \Jet\PHPJet::$app->store->getNumberOfQueries() . ' -->';
    }
    // Just for Fun!
    echo '<!-- ' . \Jet\PHPJet::$app->system->getEngineVersion() . ' -->';
}