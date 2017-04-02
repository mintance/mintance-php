<?php

$people_id = rand(1, 100); // Id from e-commerce site.

$mintance = new \Mintance\Mintance('API_KEY');

$mintance->people->setIdentifier($people_id);

$mintance->track('Add to Cart', [
	'product_id' => 1
]);