<?php

$urls = new \Jet\App\Engine\Core\Urls();
$urls->setController('/auth', 'Auth', ['test']);
$urls->setController('/', 'Main', ['test']);
