<?php

use Jet\App\App;
use Jet\App\Engine\Config\Config;
use Jet\PHPJet;
//
$version = phpversion();
if ((float)$version < 7.3) {
    exit('Version required: 7.3, but installed: ' . $version);
}
//
date_default_timezone_set('UTC');
$start = microtime(true);
//
// Root directory
const ROOT = __DIR__ . '/../';
// Web (static) directory
const WEB = ROOT . 'web/';
// Application directory
const APP = ROOT . 'app/';
// Home directory
const MVC = ROOT . 'app/mvc/';
// Engine directory
const ENGINE = ROOT . 'app/engine/';
// Images
const IMAGES = 'storage/';
// thumbnails
const THUMBNAILS = 'storage/thumbnails/';

// Engine Core
require_once(ROOT . 'PHPJet.php');
require_once(ROOT . 'app/App.php');
// Load Composer's components
require_once(ROOT . 'vendor/autoload.php');
// Engine version
require_once ENGINE . 'config/version.php';

// todo allow this only in development mode
$functionName = isset($argv) && !empty($argv[1]) ? $argv[1] : 'start';
PHPJet::init($functionName);
if (method_exists(PHPJet::$app, $functionName)) {
    echo call_user_func([PHPJet::$app, $functionName], $argv);
} else {
    echo "Method does not exist";
}

// Some debug info (temporary)
if (false && !PHPJet::$app->system->request->getPOST() ) {
    if (Config::$dev['debug']) {
        echo '<!-- Generation time: ' . (microtime(true) - $start) . ' s. -->';
        echo '<!-- SQL-queries: ' . PHPJet::$app->store->getNumberOfQueries() . ' -->';
    }
    // Just for Fun!
    echo '<!-- ' . PHPJet::$app->system->getEngineVersion() . ' -->';
}