<?php

$urls = new \Jet\App\Engine\Core\Urls();
$urls->setUrl('/', 'action', 'login', ['POST' => [
    'email' => ['email', false],
    'password' => ['password', true]
]]);

$urls->setUrl('/check', 'action', 'check');
$urls->setUrl('/logout', 'action', 'logout');