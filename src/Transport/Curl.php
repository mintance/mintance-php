<?php

namespace Mintance\Transport;

use Mintance\Exceptions\Exception;

class Curl extends AbstractTransport {

	protected function _execute($args) {

		if(empty($this->_endpoint)) {
			throw new Exception('Empty endpoint.');
		}

		$ch = curl_init();

		curl_setopt(
			$ch,
			CURLOPT_URL,
			$this->_options['protocol'].'://'.$this->_options['url'].'/v'.$this->_options['version'].'/'.$this->_endpoint
		);

		$args['token'] = $this->_token;

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connect_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));

		$response = curl_exec($ch);

		if($response === false) {
			throw new Exception('Curl error: '.curl_error($ch));
		}

		$response = json_decode($response, true);

		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($response_code != 201) {
			throw new Exception('Sending error: '.$response['message']);
		}

		return $response;
	}
}