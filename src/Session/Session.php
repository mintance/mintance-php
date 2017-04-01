<?php

namespace Helpers;

use Mintance\Transport\AbstractTransport;

class Session {

	protected $_transport;

	protected $_session_id;

	public function __construct(AbstractTransport &$transport) {
		$this->_transport = $transport;

		$this->_subscribe();
	}

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