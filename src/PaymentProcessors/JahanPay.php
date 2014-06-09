<?php

namespace Bigsinoos\ParsiPayment\PaymentProcessors;

use ParsiPayment\AbstractProcessor;
use ParsiPayment\PaymentProcessorInterface;


class JahanPay extends AbstractProcessor implements PaymentProcessorInterface{

	/**
	 * Needed request data
	 *
	 * 
	 */
	protected $request_needs = [
		'method'	=>	"string",
		'apu'		=>	"string",
	]


	public function requestPayment()
	{
		
	}

	public function setAmount( $amount )
	{

	}

	public function setRedirect( $to )
	{

	}

	public function doVerify (array $data = [])
	{

	}
	public function getPaymentUrl()
	{

	}

}