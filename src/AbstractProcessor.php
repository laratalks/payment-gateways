<?php

namespace Bigsinoos\ParsiPayment;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

abstract class AbstractProcessor {

	/**
	 * Get Guzzle client 
	 * 
	 * @return Client
	 */
	protected function getGuzzleClient()
	{
		return new Client();
	}

	/**
	 * Magic method used for throwing errors
	 *
	 * @param string $method_name
	 * @param array $method_arguments
	 * @return void || Exception
	 */
	public function __call( $method_name , $method_arguments = [ ])
	{
		$method_name = "\\SerializeIr\Services\\ParsiPayment\\Exceptions\\" . ucfirst($method_name);

		if ( class_exists($method_name)) 
			return new $method_name(( ! empty($method_arguments[0]) ? $method_arguments[0] : '' ));

		throw new \Exception( "Method not found.");
	}

	/**
	 * Assert that the request has needed data
	 *
	 * @return bool
	 */
	protected function assertRequestNeeds()
	{
		foreach($this->request_needs as $need)
		{
			if( is_null($this->$need) )
			{
				throw $this->RequestNeedException(
					"You have not set $" . $need . " variable"
				);
				return false;
			}
			return true;
		}
	}
}
