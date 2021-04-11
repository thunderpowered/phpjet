<?php

// todo maybe create separate files for each controller
$urls = new \Jet\App\Engine\Core\Urls();

/* <==== MAIN CONTROLLER ====> */
$urls->setAction('Main', '/', 'Home');

/* <==== AUTH CONTROLLER ==== > */
$urls->setAction('Auth', '/', 'Login', [
    'POST' => [
        'email' => ['email', false],
        'password' => ['password', true]
    ],
    'GET' => [
        'cho' => ['not_empty', false]
    ]
]);