<?php

namespace Mintance\Producers;

use Mintance\Exceptions\Exception;

class Event extends AbstractProducer {

	public function track($name, array $params = []) {
		$response = $this->_push($this->_buildEvent($name, 'custom-event', $params));

		if(!empty($response['event_id'])) {
			return $response['event_id'];
		} else {
			throw new Exception('Event sending error.');
		}
	}

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

	protected function _buildEvent($name, $type, array $params = []) {
		return [
			'event_name' => $name,
			'event_type' => $type,
			'event_params' => $params
		];
	}

	protected function _push(array $event) {
		$this->_transport->setEndpoint('events');

		return $this->_transport->execute($event);
	}
}