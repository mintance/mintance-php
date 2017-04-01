<?php

namespace Mintance\Producers;

use Mintance\Transport\AbstractTransport;

abstract class AbstractProducer {

	protected $_transport;

	public function __construct(AbstractTransport &$transport) {
		$this->_transport = $transport;
	}
}