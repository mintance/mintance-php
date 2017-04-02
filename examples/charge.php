<?php

$mintance = new \Mintance\Mintance('API_KEY');

$mintance->charge(100, [
	'currency' => 'USD',
	'products' => [
		[
			'id' => 10,
			'quantity' => 2
		]
	]
]);