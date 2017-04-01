<?php

namespace Mintance;

use Helpers\Session;
use Mintance\Exceptions\Exception;
use Mintance\Producers\Event;
use Mintance\Producers\People;
use Mintance\Transport\Curl;

class Mintance {

	public $people;

	protected $_token;

	protected $_options = [
		'url' => 'api.mintance.com',
		'version' => 1,
		'protocol' => 'https',
		'debug' => false,
		'transport' => 'curl'
	];

	protected $_event;

	protected $_session;

	protected $_transport;

	public function __construct($token, array $options = []) {

		$this->_options = array_replace(
			$this->_options,
			$options
		);

		$this->_token = $token;

		$this->_initTransport();

		$this->people = new People($this->_transport);

		$this->_event = new Event($this->_transport);

		$this->_session = new Session($this->_transport);
	}

	public function track($name, array $params = []) {
		$this->_event->track($name, $params);
	}

	public function charge() {

	}

	protected function _initTransport() {
		switch ($this->_options['transport']) {
			case 'curl':
			default:
				$this->_transport = new Curl($this->_token, $this->_options);
		}
	}
}