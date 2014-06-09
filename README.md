Parsi Payment 
=========

Parsi Payment Package is a highly testable , flexible and resuable PHP Composer Package for working with Iranian payment service providers like Payline , Zarinpal, Parspal and Jahanpay.

Connecting to Payment web services may be easy but testing and error handling needs bunch of coding that may depend on your application domain scope. with Parsi Payment you can easily solve this problem in an OO way:

  - Objected oriented way for handling payments in your application
  - Payment Classes really care about errors , every failing action throws an sepcific Exception.
  - Every Exception extends the base ParsiPaymentException
  - Visible Methods for different payment providers are the same. you can easily switch between providers in your app! 

> Parsi Payment classes implement an Interface to act with your application, simply it means you can switch to different payment service providers without too much knowing about their connection layer, you just set mandotary dynamic variables (like amount of payment) and here you go!!!!!


USAGE
--------------
### Request a payment

```sh
$paymenter = new \Bigsinoos\ParsiPayment\PaymentProcessors\Payline;
$paymenter->setAmount(1500)
          ->setRedirect("http://example.com/payements/?order_id=555");
          ->getPaymentUrl(); // User Payment url ,you can redirect user to this url 

header("Location: " . $paymenter) ; // User goes to provider site and pays
```
### Request a payment with handling errors

```sh
<?php

use Bigsinoos\ParsiPayment\PaymentProcessors\Payline;
use Bigsinoos\ParsiPayment\Exceptions;

// If you want to use another payment provider 
// Just change this line! 
$paymenter = new Payline;

try
{
	$paymenter->setAmount(15000);
	$paymenter->setRedirect("http://mysite.com/payment/complete?order_id=255");

	header("Location: " . $paymenter->getPatmentUrl(););

}
catch (ParsiPaymentException $e)
{

	// easily get error message with its error key : payline.invalid_api
	$message =  Lang::get($e->getMessage());

	// or make it really customized! these keys are the same
	// for all providers
	switch( $e->getMessage() )
	{
		case $paymenter::UNABLLE_TO_CONNECT:
			print "متاسفانه سیستم نمیتواند به درگاه پرداخت وصل شود. لطفا از دگاه دیگری استفاده کنید";
			break;

		case $paymenter::INVALID_API_KEY:
			print "Invalid API key";
			break;

		default:
			print $e->getMessage();
			break;
	}
}

```
What's the problem
------------------
You may say that you can do the job of all this classes in 10 lines of code , ofcourse you can , but after using this classes you will be able to to do it in 3 lines :). but it is not the main reason, trust me you will go to a nightmare when you want to handle connection errors , validate connection variables , validate request response and handle response errors , validate verify params and handle verfiy response and errors, and use all of this in another project or part of your app. 

Above approach is definitely an anti-pattern , and simply breaks *Single Responsibility Pricnciple*  in *SOLID Method* for software development.

You should be able to catch all type of errors , You should be able to easily switch between payement providers, and you should be able to reuse your code in other parts , these classes provide an objected oriented, testable and flexible approach for you.

How it works
------------
Every Payment service provider has these three steps in his wrok flow:

 - Requesting a payment
 - Proccessing user Payment
 - Verifying user payment

### 1. Requesting a payment 
Regurarly when you request for a payment , you provide some data with your request , this data may contain an api-key or a set of MerchantID and Password to identify your merchant account, the amount of payment and a redirect back url that can contain your payment specific data (like order_id , user_id , etc) , these parameters are mandotary for each provider but some may have more params! .

### 2. Processing payment
Your response (in most payment service providers) is a payment id number (some call it reservation number), this is the id that is reserved for your requested payment , you redirect your user to the provider site with the help of this unique id. then user pays and returns to your site (to your redirect back request parameter). 

### 3. Verifying payment
After successfull redirection to your website, you should verify user payment to mark his order as  completed. Most providers force a post request to your redirect address with a *transaction_id* field , you send this transaction id back to the provider backend and get some responses then make your changes based on the responses.


License
----

Free under MIT
