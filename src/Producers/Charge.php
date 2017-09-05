<?php

namespace Mintance\Producers;

use Mintance\Exceptions\Exception;

/**
 * Class Charge
 * @package Mintance\Producers
 */
class Charge extends Event {

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
	public function track($amount, array $params = []) {

		$response = $this->_push(
			$this->_buildChargeEvent($amount, $params)
		);

		if(!empty($response['charge_id'])) {
			return $response['charge_id'];
		} else {
			throw new Exception('Charge sending error.');
		}
	}

	/**
	 * Function builds charge event object by default fields.
	 *
	 * @param float $value Charge value
	 * @param array $params Custom params.
	 *
	 * @return array Event Data object (array).
	 */
	protected function _buildChargeEvent($value, array $params = []) {

		$charge = [
			'value' => $value
		];

		if(!empty($params['currency'])) {
			$charge['currency'] = $params['currency'];
		}

		if(!empty($params['products']) && is_array($params['products'])) {
			$charge['products'] = $params['products'];
		}

		return array_merge($charge, [
			'event_type' => 'charge-event',
			'charge_params' => $params
		]);
	}

	/**
	 * Function send's event to mintance.
	 *
	 * @param array $event Event data.
	 *
	 * @return \Mintance\Transport\AbstractTransport
	 */
	protected function _push(array $event) {

		$this->_transport->setEndpoint('charges');

		return $this->_transport->execute($event);
	}
}