<?php

require_once '../autoloader.php';

$mintance = new \Mintance\Mintance('d3be88e277c4d7a3b9f92c262d90683c1e49e5a1d50c464f771e264de43f56be', [
	'url' => 'api.mintance.dev',
	'protocol' => 'http'
]);


$people_id = '123'; // Id from e-commerce site.

$mintance->people->setIdentifier($people_id);
//
//$mintance->track('Add to Cart', [
//	'product_id' => 1
//]);
//
//if($mintance->people->get()['id'] != '58e0be1f48177e953b8b473d') {
//	throw new \Exception('Invalid people_id');
//}


$mintance->people->set([
	'email' => 'test@email.com',
	'name' => '',
	'phone' => '23'
]);