<?php

namespace Mintance\Producers;

use Mintance\Transport\AbstractTransport;

/**
 * Class People
 * @package Mintance\Producers
 */
class People extends AbstractProducer {

	/**
	 * @var string Generated Permanent ID.
	 */
	protected $_permanent_id;

	/**
	 * @var string Visitor ID from mintance.
	 */
	protected $_visitor_id;

	/**
	 * @var string People ID from mintance.
	 */
	protected $_people_id;

	/**
	 * @var string Custom people identifier.
	 */
	protected $_identifier;

	/**
	 * @var string People type.
	 */
	protected $_type = 'visitor';

	/**
	 * @var array People data from mintance.
	 */
	protected $_people = [];

	/**
	 * @var array Default people fields in Mintance.
	 */
	protected $_default_fields = [
		'name', 'first_name', 'last_name', 'middle_name', 'email', 'phone'
	];

	/**
	 * People constructor.
	 *
	 * Generates permanent_id on init & subscribe to data transporter.
	 *
	 * @param \Mintance\Transport\AbstractTransport $transport
	 */
	public function __construct(AbstractTransport $transport) {
		parent::__construct($transport);

		$this->_permanent_id = $this->_generatePermanentId();

		$this->_subscribe();
	}

	/**
	 * Function sets custom people identifier.
	 * You can identify & merge your visitors using any field, like id in your own system, or email, etc..
	 *
	 * @param string $identifier
	 */
	public function setIdentifier($identifier) {

		$this->_identifier = $identifier;

		$this->_transport->register('execute:before', function (&$args) {
			if(empty($this->_people_id)) {
				$args['mintance_identifier'] = $this->_identifier;
			}
		});
	}

	/**
	 * Function merge's your visitor with mintance peoples.
	 * And returns it's people_id in mintance.
	 *
	 * @param string $identifier Any custom identifier.
	 * @return string People ID.
	 */
	public function identify($identifier) {

		if(!empty($this->_people_id)) {
			return $this->_people_id;
		}

		$this->_transport->setEndpoint('people/identify');

		$data = [
			'identifier' => $identifier
		];

		if(!empty($this->_visitor_id)) {
			$data['visitor_id'] = $this->_visitor_id;
		}

		$response = $this->_transport->execute($data);

		$this->_people = $response['people'];

		$this->_type = 'people';

		return $this->_people_id = $response['people_id'];
	}

	/**
	 * Update people data.
	 *
	 * @param array $args People fields.
	 */
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

		if(empty($this->get()['id'])) {
			if(!empty($this->_identifier)) {
				$this->identify($this->_identifier);
			}
		}

		$this->_transport->setEndpoint('people/'.$this->get()['id']);

		$this->_transport->execute($data);
	}

	/**
	 * Return's array with people data in mintance.
	 *
	 * @return array
	 */
	public function get() {
		return array_replace(
			$this->_people, [
			'id' => !empty($this->_people_id) ? $this->_people_id : $this->_visitor_id,
			'type' => $this->_type
		]);
	}

	/**
	 * Subscription to data transporter to recognize & save people id or set it to request otherwise.
	 */
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

	/**
	 * Permanent ID generator.
	 *
	 * @return string Permanent ID.
	 */
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