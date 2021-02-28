<?php

$urls = new \Jet\App\Engine\Core\Urls();
$urls->setUrl('/', 'action', 'login');
$urls->setUrl('/check', 'action', 'check');
$urls->setUrl('/logout', 'action', 'logout');