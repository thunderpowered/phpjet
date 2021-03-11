<?php

$urls = new \Jet\App\Engine\Core\Urls();
$urls->setAction('Auth', '/', 'login', [
    'POST' => [
        'email' => ['email', false],
        'password' => ['password', true]
    ]
]);