<?php

namespace Mintance;

use Mintance\Exceptions\Exception;
use Mintance\Producers\Event;
use Mintance\Producers\People;
use Mintance\Transport\AbstractTransport;
use Mintance\Transport\Curl;
use Mintance\Session\Session;

/**
 * Main mintance class.
 *
 * @package Mintance
 */
class Mintance {

	/**
	 * @var \Mintance\Producers\People People producer object.
	 */
	public $people;

	/**
	 * @var string API key from your project.
	 */
	protected $_token;

	/**
	 * @var array Default options.
	 */
	protected $_options = [
		'url' => 'api.mintance.com',
		'version' => 1,
		'protocol' => 'https',
		'debug' => false,
		'transport' => 'curl'
	];

	/**
	 * @var \Mintance\Producers\Event Event producer object.
	 */
	protected $_event;

	/**
	 * @var \Mintance\Session\Session Session store object.
	 */
	protected $_session;

	/**
	 * @var AbstractTransport Transport object.
	 */
	protected $_transport;

	/**
	 * Mintance constructor.
	 *
	 * @param string $token API key from project settings.
	 * @param array $options
	 */
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

	/**
	 * Track custom event.
	 *
	 * @param string $name Event name.
	 * @param array $params Some additional params.
	 * @return string Returns: event_id
	 *
	 * @throws Exception If something wrong
	 */
	public function track($name, array $params = []) {
		return $this->_event->track($name, $params);
	}

	/**
	 * Track purchase event.
	 *
	 * @param double $amount Payment amount.
	 * @param array $params Some additional params.
	 * Example of params that recognising by tool.
	 * {
	 *   "currency": "USD",
	 *   "products": [
	 *      [
	 *          'id' => 1,
	 *          'quantity' => 2,
	 *      ]
	 *   ]
	 * }
	 *
	 * @return string Returns: charge_id
	 *
	 * @throws Exception If something wrong
	 */
	public function charge($amount, array $params = []) {
		return $this->_event->charge($amount, $params);
	}

	/**
	 * Track form submit event.
	 *
	 * @param array $data Form fields & values. Key-value array.
	 *
	 * @return string Returns: event_id
	 *
	 * @throws Exception If something wrong
	 */
	public function formSubmit(array $data) {
		return $this->_event->formSubmit($data);
	}

	/**
	 * Selecting transport.
	 *
	 * For now only curl.
	 */
	protected function _initTransport() {
		switch ($this->_options['transport']) {
			case 'curl':
			default:
				$this->_transport = new Curl($this->_token, $this->_options);
		}
	}
}