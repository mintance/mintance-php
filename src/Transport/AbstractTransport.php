<?php

namespace Mintance\Transport;

use Mintance\Core\DispatcherLoop;

/**
 * Class AbstractTransport
 * @package Mintance\Transport
 *
 * @method AbstractTransport execute(mixed $data)
 */
abstract class AbstractTransport extends DispatcherLoop {

	protected $_token;

	protected $_options = [];

	protected $_connect_timeout = 5;

	protected $_timeout = 30;

	protected $_endpoint;

	public function __construct($token, array $options) {
		$this->_token = $token;
		$this->_options = $options;
	}

	public function setEndpoint($endpoint) {
		$this->_endpoint = $endpoint;

		return $this;
	}

	protected abstract function _execute($args);
}