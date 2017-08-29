<?php

namespace Mintance\Session;

use Mintance\Transport\AbstractTransport;

/**
 * Class Session
 * @package Mintance\Session
 */
class Session {

	/**
	 * @var \Mintance\Transport\AbstractTransport Transporter object.
	 */
	protected $_transport;

	/**
	 * @var string Session ID.
	 */
	protected $_session_id;

	/**
	 * Session constructor.
	 * @param \Mintance\Transport\AbstractTransport $transport
	 */
	public function __construct(AbstractTransport &$transport) {
		$this->_transport = $transport;

		$this->_subscribe();
	}

	/**
	 * Subscribes to sending & receiving data to mintance to add or read session_id.
	 */
	protected function _subscribe() {
		$this->_transport->register('execute:before', function (&$args) {
			if(!empty($this->_session_id)) {
				$args['session_id'] = $this->_session_id;
			}
		});

		$this->_transport->register('execute:after', function ($args) {
			if(!empty($args['session_id'])) {
				$this->_session_id = $args['session_id'];
			}
		});
	}
}