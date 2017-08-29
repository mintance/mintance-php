<?php

namespace Mintance\Producers;

use Mintance\Exceptions\Exception;

/**
 * Class Event
 * @package Mintance\Producers
 */
class Event extends AbstractProducer {

	/**
	 * Tracking custom event function.
	 *
	 * @param string $name Event name.
	 * @param array $params Event params.
	 *
	 * @return string Event ID if success.
	 *
	 * @throws \Mintance\Exceptions\Exception Throws exception if something goes wrong.
	 */
	public function track($name, array $params = []) {
		$response = $this->_push($this->_buildEvent($name, 'custom-event', $params));

		if(!empty($response['event_id'])) {
			return $response['event_id'];
		} else {
			throw new Exception('Event sending error.');
		}
	}

	/**
	 * Tracking Purchase event.
	 *
	 * @param float $amount Amount of money.
	 * @param array $params Purchase params
	 *  Default params are: currency, products
	 *
	 * @return string Charge ID if success.
	 *
	 * @throws \Mintance\Exceptions\Exception Throws exception if something goes wrong.
	 */
	public function charge($amount, array $params = []) {

		$response = $this->_push($this->_buildEvent('Charge', 'charge', array_merge($params, [
			'value' => $amount
		])));

		if(!empty($response['charge_id'])) {
			return $response['charge_id'];
		} else {
			throw new Exception('Charge sending error.');
		}
	}

	/**
	 * Tracking form submission event.
	 *
	 * @param array $data Form fields data.
	 *
	 * @return string Event ID if success.
	 * @throws \Mintance\Exceptions\Exception Throws exception if something goes wrong.
	 */
	public function formSubmit(array $data) {

		$response = $this->_push(array_merge(
			$this->_buildEvent('Form Submit', 'form-submit'),
			[
				'form_data' => $data
			]
		));

		if(!empty($response['event_id'])) {
			return $response['event_id'];
		} else {
			throw new Exception('Charge sending error.');
		}
	}

	/**
	 * Function builds event object by default fields.
	 *
	 * @param string $name Event name
	 * @param string $type Event type
	 * @param array $params Custom params.
	 *
	 * @return array Event Data object (array).
	 */
	protected function _buildEvent($name, $type, array $params = []) {
		return [
			'event_name' => $name,
			'event_type' => $type,
			'event_params' => $params
		];
	}

	/**
	 * Function send's event to mintance.
	 *
	 * @param array $event Event data.
	 *
	 * @return \Mintance\Transport\AbstractTransport
	 */
	protected function _push(array $event) {

		$this->_transport->setEndpoint('events');

		return $this->_transport->execute($event);
	}
}