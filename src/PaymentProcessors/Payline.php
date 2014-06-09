<?php

namespace Bigsinoos\ParsiPayment\PaymentProcessors;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use ParsiPayment\AbstractProcessor;
use ParsiPayment\PaymentProcessorInterface;

class Payline 
extends AbstractProcessor 
implements PaymentProcessorInterface {

	/**
	 * POST gate way address
	 *
	 * @var string
	 */
	const GATEWAY = "http://payline.ir/payment/";

	/**
	 * POST test gate way
	 *
	 * @var string
	 */
	const TEST_GATEWAY = "http://payline.ir/payment-test/";
	
	/**
	 * Payline API key
	 * 
	 * @var string 
	 */
	const API_KEY = "";

	/**
	 * Payline test API_KEY
	 * 
	 * @var string
	 */
	const TEST_API_KEY = "adxcv-zzadq-polkjsad-opp13opoz-1sdf455aadzmck1244567";


	/**
	 * Request status code is not 200
	 * 
	 * @var string
	 */
	const INVALID_STATUS_CODE = "payments.payline.bad_status_code";

	/**
	 * Request API key is not valid 
	 *
	 * @var string
	 */
	const INVALID_API_KEY = "payments.payline.invalid_api";

	/**
	 * Request amount is not integer or is lesser than 1000
	 *
	 * @var string
	 */
	const INVALID_AMOUNT = "payments.payline.invalid_amount";

	/**
	 * Request redirect is not valid
	 *
	 * @var string
	 */
	const INVALID_REDIRECT = "payments.payline.invalid_rediect";

	/**
	 * You have not set redirect param
	 *
	 * @var string
	 */
	const REDIRECT_NOTSET = "payments.payline.redirect_not_set";

	/**
	 * You have not set amount param
	 *
	 * @var string
	 */
	const AMOUNT_NOTSET = "payment.payline.amount_not_set";

	/**
	 * Unable to connect to payline network due to nework error
	 *
	 * @var string
	 */
	const UNABLE_TO_CONNECT = "payments.payline.unable_to_connect";

	/**
	 * payline sent an unknown error
	 *
	 */
	const UNKNOWN_ERROR = "payments.payline.unknown";

	/**
	 * Verify Invalid transaction id
	 *
	 * @var string
	 */
	const INVALID_TRANS_ID = "payments.payline.invalid_trans_id";

	/**
	 * Verify Invalid transaction
	 *
	 * @var string
	 */
	const INVALID_TRANS = "payments.payline.invalid_transaction";

	/**
	 * Verify Invalid id_get
	 *
	 * @var string
	 */
	const INVALID_ID_GET = "payments.payline.invalid_id_get";

	/**
	 * amount of transaction
	 *
	 * @var integer 
	 */
	protected $amount;

	/**
	 * Test mode
	 *
	 * @var bool
	 */
	protected $test_mode = false;

	/**
	 * Redirect address
	 *
	 * @var
	 */
	protected $redirect;

	/**
	 * Needed request variables
	 *
	 * @var array
	 */
	protected $request_needs = ["amount","redirect"];

	/**
	 * id_get that payline returns
	 *
	 * @param integer
	 */
	protected $id_get;

	/**
	 * Protected request return trans_id
	 *
	 * @var
	 */
	protected $return_trans_id;

	/**
	 * Protected request return trans_id
	 *
	 * @var
	 */
	protected $return_id_get;

	/**
	 * Amount and redirect can be set through constructor
	 *
	 * @param integer $amount
	 * @param string $redirect
	 * @return void
	 */
	public function __construct( $amount = null , $redirect = null)
	{
		if ( ! is_null( $amount )) $this->setamount($amount);
		if ( ! is_null( $redirect )) $this->setRedirect($redirect);
	}

	/**
	 * Set amount we only accept integer amount 
	 *
	 * @param integer $amount
	 * @return self
	 */
	public function setAmount( $amount )
	{
		if ( gettype( $amount ) != "integer" || $amount < 1000  )
		{
			throw $this->RequestNeedException('$amount variable should be of type of integer and bigger than 1000');
			return false;
		}
		$this->amount = $amount;
		return $this;
	}

	/**
	 * Set redirect , we only accept string values that
	 * start with "http"
	 *
	 * @param string $redirect
	 * @return self
	 */
	public function setRedirect( $redirect )
	{
		if ( strpos (strtolower($redirect), "http") !== 0)
		{
			throw $this->RequestNeedException("$redirect must be a valid http url with query strings");
			return false;
		}
		$this->redirect = $redirect;
		return $this;
	}

	/**
	 * Send request to payline
	 *
	 *
	 * @return string
	 **/
	public function requestPayment()
	{
		if ( ! $this->assertRequestNeeds() )
			return false;

		$client = $this->getGuzzleClient();

		try
		{
			$client = $this->getGuzzleClient();

			$request = $client->createRequest("POST", $this->getGateWay() . "gateway-send");

			$body = $request->getBody();

			$body->setField('api', $this->getApiKey() );
			$body->setField('amount', $this->amount);
			$body->setField('redirect',$this->redirect);

			$response = $client->send( $request );
			$this->handleRequestResponse ( $response );
			return $this;
		}
		catch (TransferException $e)
		{
			throw $this->ConnectionException(self::UNABLE_TO_CONNECT);
		}
	}

	/**
	 * handle payline request response
	 * 
	 * @param $response
	 * @return self (chain)
	 */
	protected function handleRequestResponse ( $response )
	{
		if ( $response->getStatusCode() != 200 )
			throw $this->RemoteStatusException(self::INVALID_STATUS_CODE);

		$integer_response = (string) $response->getBody();

		switch ( $integer_response )
		{
			case "-1":
				throw $this->RequestException(self::INVALID_API_KEY);

			case "-2":
				throw $this->RequestException(self::INVALID_AMOUNT);

			case "-3":
				throw $this->RequestException(self::INVALID_REDIRECT);

			case "-4":
				throw $this->RequestException(self::INVALID_MERCHANT);

			default:
				if( $integer_response <= 0 || ! is_numeric($integer_response))
					throw $this->RequestException(self::UNKNOWN_ERROR);

				$this->id_get = $integer_response;
				return $integer_response;
		}
	}

	/**
	 * set test mode
	 *
	 * @return void
	 * @return self (chain)
	 */
	public function setForTest()
	{
		$this->test_mode = true;
		return $this;
	}

	/**
	 * If in test mode
	 * 
	 * @return bool
	 */
	public function isTest()
	{
		return $this->test_mode;
	}

	/**
	 * Get request api key
	 * 
	 * @return string
	 */
	protected function getApiKey ()
	{
		if( empty(self::API_KEY) && ! $this->isTest())
			throw $this->RequestNeedException(
				"API_KEY is not set you can switch to test mode with Payline::setForTest()"
			);

		return ( $this->isTest() ? self::TEST_API_KEY : self::API_KEY);
	}

	/**
	 * Get POST Backend
	 *
	 * @return string
	 */
	protected function getGateWay()
	{
		if (empty(self::GATEWAY) && ! $this->isTest())
			throw $this->RequestNeedException(
				"GATEWAY constant can not be empty, you can switch to test mode",
				1
			);

		return ( $this->isTest() ? self::TEST_GATEWAY : self::GATEWAY);
	}

	/**
	 * Get Payment url
	 *  
	 * @return string
	 */
	public function getPaymentUrl()
	{
		// If we already send the request we have id_get 
		// so we don't do it again
		if (is_null($this->id_get))
			$this->requestPayment();

		return $this->getGateWay() . 'gateway-' . $this->id_get;
	}

	/**
	 * Get payment id
	 *
	 * @var integer
	 */
	public function getPaymentId()
	{
		if( is_null($this->id_get))
			$this->requestPayment();

		return $this->id_get;
	}

	/**
	 * Verify Payment
	 *
	 * @return bool
	 */
	public function doVerify ( array $return_post)
	{
		if ( ! $this->setReturnParams( $return_post ))
			return false;
		try
		{
			$client = $this->getGuzzleClient();

			$request = $client->createRequest("POST", $this->getGateWay() . 'gateway-result-second');

			$body = $request->getBody()
							->setField('api',$this->getApiKey())
							->setField('id_get', $this->return_id_get)
							->setField('trans_id',$this->return_trans_id);

			$response = $client->send( $request );
			return $this->handleVerifyRespnse( $response );
		}
		catch (TransferException $e)
		{
			throw $this->ConnectionException(self::UNABLE_TO_CONNECT);
		}
	}

	/**
	 * Assert that return has needed values
	 * 
	 * @return bool
	 */
	protected function setReturnParams( array $params )
	{
		if ( empty($params['trans_id']) || empty($params['id_get']) )
			throw $this->VerifyException(self::INVALID_TRANS_ID);

		$this->return_trans_id = $params['trans_id'];
		$this->return_id_get = $params['id_get'];

		return true;
	}

	/**
	 * Handle verify response
	 *
	 * @param GuzzleHttp\Response
	 */
	protected function handleVerifyRespnse ( $response)
	{
		$integer_response = (string) $response->getBody();

		switch ( $integer_response )
		{
			case '-1':
				throw $this->VerifyException(self::INVALID_API_KEY);

			case '-2':
				throw $this->VerifyException(self::INVALID_TRANS_ID);

			case '-3':
				throw $this->VerifyException(self::INVALID_ID_GET);

			case '-4':
				throw $this->VerifyException(self::INVALID_TRANS);

			case '1':
				return true;

			default:
				throw $this->VerifyException(self::UNKNOWN_ERROR);
		}
	}

}