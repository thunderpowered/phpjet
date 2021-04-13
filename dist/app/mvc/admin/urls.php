<?php

$urls = new \Jet\App\Engine\Core\Urls();
$urls->setController('/', 'Main');
$urls->setController('/auth', 'Auth');
$urls->setController('/admin', 'Admin');
