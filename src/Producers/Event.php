<?php

namespace Mintance\Producers;

class Event extends AbstractProducer {

	public function track($name, array $params = []) {

		$event = $this->_buildEvent($name, 'custom-event', $params);

		$this->_push($event);
	}

	public function charge($amount, array $params = []) {

		$event = $this->_buildEvent('Charge', 'charge', array_merge($params, [
			'value' => $amount
		]));

		$this->_push($event);
	}

	public function formSubmit(array $data) {
		$event = array_merge(
			$this->_buildEvent('Form Submit', 'form-submit'),
			[
				'form_data' => $data
			]
		);

		$this->_push($event);
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

		$this->_transport->execute($event);
	}
}