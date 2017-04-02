<?php

namespace Mintance\Producers;

use Mintance\Exceptions\Exception;
use Mintance\Transport\AbstractTransport;

class People extends AbstractProducer {

	protected $_permanent_id;

	protected $_visitor_id;

	protected $_people_id;

	protected $_identifier;

	protected $_type = 'visitor';

	protected $_people = [];

	protected $_default_fields = [
		'name', 'first_name', 'last_name', 'email', 'phone'
	];

	public function __construct(AbstractTransport $transport) {
		parent::__construct($transport);

		$this->_permanent_id = $this->_generatePermanentId();

		$this->_subscribe();
	}

	public function setIdentifier($identifier) {

		$this->_identifier = $identifier;

		$this->_transport->register('execute:before', function (&$args) {
			if(empty($this->_people_id)) {
				$args['mintance_identifier'] = $this->_identifier;
			}
		});
	}

	public function identify($identifier) {

		if(!empty($this->_people_id)) {
			return $this->_people_id;
		}

		if(empty($this->_visitor_id)) {
			throw new Exception('Empty visitor_id.');
		}

		$this->_transport->setEndpoint('people/identify');

		$response = $this->_transport->execute([
			'visitor_id' => $this->_visitor_id,
			'identifier' => $identifier
		]);

		$this->_people = $response['people'];

		$this->_type = 'people';

		return $this->_people_id = $response['people_id'];
	}

	public function set(array $args) {
		$data = [
			'fields' => []
		];

		foreach ($args as $key => $value) {
			if(in_array($key, $this->_default_fields)) {
				$data[$key] = $value;
			} else {
				$data['fields'][$key] = $value;
			}
		}
	}

	public function get() {
		return array_replace(
			$this->_people, [
			'id' => !empty($this->_people_id) ? $this->_people_id : $this->_visitor_id,
			'type' => $this->_type
		]);
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