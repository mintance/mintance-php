<?php

namespace Mintance\Core;

use Mintance\Exceptions\Exception;

abstract class DispatcherLoop {

	protected $_subscribers = [];

	public function __call($name, $args) {
		if(!method_exists($this, '_'.$name)) {
			throw new Exception('Method is undefined.');
		}

		$args = array_shift($args);

		if(!empty($this->_subscribers[$name.':before'])) {
			foreach ($this->_subscribers[$name.':before'] as $subscriber) {
				$subscriber($args);
			}
		}

		$response = $this->{'_'.$name}($args);

		if(!empty($this->_subscribers[$name.':after'])) {
			foreach ($this->_subscribers[$name.':after'] as $subscriber) {
				$subscriber($response);
			}
		}

		return $response;
	}

	public function register($event, callable $callback) {
		if(empty($this->_subscribers[$event])) {
			$this->_subscribers[$event] = [];
		}

		$this->_subscribers[$event][] = $callback;
	}

	public function clear() {
		$this->_subscribers = [];
	}
}