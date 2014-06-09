<?php

namespace Bigsinoos\ParsiPayment;

interface PaymentProcessorInterface {

	/**
	 * Ammount of payment every gateway definitely needs this
	 *
	 * @return self
	 */
	public function setAmount( $amount );

	/**
	 * Redirect url after payment
	 * Definitely any gateway needs a redirect url
	 * 
	 * @return self
	 */
	public function setRedirect( $redirect );

	/**
	 * Request payment 
	 * Definitely any gateway needs to ask him for a new payment
	 * 
	 * @return self
	 */
	public function requestPayment();

	/**
	 * Verify payment
	 * Definitely any gateway uses something to make you able to verify
	 *
	 * @return bool
	 */
	public function doVerify( array $verify_data );

	/**
	 * Any payment gateway gives you a payment url
	 * to redirect user to it
	 *
	 * @return string
	 */
	public function getPaymentUrl();

}