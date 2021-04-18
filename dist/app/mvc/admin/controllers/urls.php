<?php

use Jet\App\Engine\Core\Urls;

$urls = new Urls();

/* <======== MAIN CONTROLLER ========> */
$urls->setAction('Main', '/', 'Home', [
    'GET' => []
], false);

/* <======== AUTH CONTROLLER ======== > */
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
$urls->setAction('Auth', '/logout', 'Logout', [
    'POST' => []
]);

/* <======== ADMIN CONTROLLER ======== > */
$urls->setAction('Admin', '/{ADMIN_ID}/settings/{SETTINGS}', 'Settings', [
    'GET' => []
    // todo file
]);

/* <======== RECORD CONTROLLER ======== > */
$urls->setAction('Record', '/{RECORD_ID}', 'Record', [
    'GET' => [
        'mode' => [null, true]
    ]
], false);