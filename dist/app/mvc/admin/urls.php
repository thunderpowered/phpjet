<?php

$urls = new \Jet\App\Engine\Core\Urls();
$urls->setController('/', 'Main', ['test']);
$urls->setController('/auth', 'Auth', ['test']);
