<?php

namespace Mintance\Producers;

use Mintance\Transport\AbstractTransport;

/**
 * Class AbstractProducer
 * @package Mintance\Producers
 */
abstract class AbstractProducer {

	/**
	 * @var \Mintance\Transport\AbstractTransport Transporter object.
	 */
	protected $_transport;

	/**
	 * AbstractProducer constructor.
	 * @param \Mintance\Transport\AbstractTransport $transport
	 */
	public function __construct(AbstractTransport &$transport) {
		$this->_transport = $transport;
	}
}