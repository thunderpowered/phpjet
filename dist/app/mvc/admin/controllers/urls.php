<?php

$urls = new \Jet\App\Engine\Core\Urls();
$urls->setAction('Auth', '/', 'Login', [
    'POST' => [
        'email' => ['email', false],
        'password' => ['password', true]
    ],
    'GET' => [
        'cho' => ['not_empty', false]
    ]
]);