<?php

namespace Mintance\Producers;

use Mintance\Transport\AbstractTransport;

class People extends AbstractProducer {

	protected $_permanent_id;

	protected $_visitor_id;

	protected $_people_id;

	protected $_type = 'visitor';

	protected $_people = [];

	public function __construct(AbstractTransport $transport) {
		parent::__construct($transport);

		$this->_permanent_id = $this->_generatePermanentId();

		$this->_subscribe();
	}

	public function identify($identifier) {

	}

	public function set(array $args) {

	}

	public function __set($key, $value) {
		// TODO: Implement __set() method.
	}

	public function get() {
		return array_replace(
			$this->_people, [
			'id' => !empty($this->_people_id) ? $this->_people_id : $this->_visitor_id,
			'type' => $this->_type
		]);
	}

	public function __get($key) {
		// TODO: Implement __get() method.
	}

	protected function _subscribe() {
		$this->_transport->register('execute:before', function (&$args) {
			if(!empty($this->_people_id)) {
				$args['people_id'] = $this->_people_id;
			} else if (!empty($this->_visitor_id)) {
				$args['people_id'] = $this->_visitor_id;
			} else {
				$args['permanent_id'] = $this->_permanent_id;
			}
		});

		$this->_transport->register('execute:after', function ($args) {
			if(!empty($args['people_id'])) {

				$this->_people_id = $args['people_id'];

				$this->_type = 'people';

			} else if(!empty($args['visitor_id'])) {

				$this->_visitor_id = $args['visitor_id'];

			}

			if(!empty($args['people'])) {
				$this->_people = $args['people'];
			}
		});
	}

	protected function _generatePermanentId() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
}