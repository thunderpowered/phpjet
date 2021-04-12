<?php

use Jet\App\Engine\Core\Urls;

$urls = new Urls();

/* <==== MAIN CONTROLLER ====> */
$urls->setAction('Main', '/', 'Home', [
    'GET' => []
]);

/* <==== AUTH CONTROLLER ==== > */
$urls->setAction('Auth', '/', 'Check', [
    'GET' => []
]);
$urls->setAction('Auth', '/login', 'Login', [
    'POST' => [
        'email' => ['email', true],
        'password' => ['password', true]
    ]
]);
$urls->setAction('Auth', '/verify', 'Verify', [
    'POST' => [
        'verification' => [null, true]
    ]
]);