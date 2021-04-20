<?php
// run this script only from command line
// do not include it into Application
// see instruction for Windows/Linux here: https://docs.phpjet.org/configure


use Jet\App\Engine\Tools\Configurator;

require_once './engine/tools/Configurator.php';
$configurator = new Configurator();
$functionName = $argv[1];
if ($functionName && method_exists($configurator, $functionName)) {
    echo call_user_func([$configurator, $functionName]);
} else {
    echo "method $functionName does not exist";
}