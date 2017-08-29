<?php

require_once 'autoloader.php';

$mintance = new \Mintance\Mintance('d3be88e277c4d7a3b9f92c262d90683c1e49e5a1d50c464f771e264de43f56be', [
	'url' => 'api.mintance.dev',
	'protocol' => 'http'
]);

$mintance->track('Test Event');

