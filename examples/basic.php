<?php

use Laratalks\PaymentGateways\Configs\Config;
use Laratalks\PaymentGateways\Configs\ProviderConfig;
use Laratalks\PaymentGateways\Configs\ProxyConfig;
use Laratalks\PaymentGateways\GatewayManager;
use Laratalks\PaymentGateways\Providers\ProviderInterface;
use Laratalks\PaymentGateways\ValueObjects\PaymentNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestResponse;
use Laratalks\PaymentGateways\ValueObjects\PaymentVerifyResponse;
use Symfony\Component\HttpFoundation\Request;

$config = (new Config())
    ->addProvider(new ProviderConfig('upal', 'fknnfgloshgwl353dkvjdoif'))
    ->addProvider(new ProviderConfig('zarinpal', 'nafngwithtgngt'))
    ->setDefaultProvider('upal');

$proxy = new ProxyConfig();
$proxy->setType(ProxyConfig::TYPE_HTTP);
$proxy->setHost('localhost');
$proxy->setPort(8123);
$proxy->setEnabled(true);


$config->setProxy($proxy);


// The facade to all abilities
$manager = new GatewayManager($config);


/**
 * Extending your own provider
 */
class ExampleProvider implements ProviderInterface
{

    public function getName()
    {
        return 'example';
    }


    public function callPaymentRequest(PaymentRequestNeeds $needs)
    {
        // call payment request and get response
        // you must  generate payment url
        // for redirecting customer to payment  gateway
        return new PaymentRequestResponse('PAYMENT_URL');
    }


    public function callVerifyRequest(PaymentNeeds $needs, Request $request = null)
    {
        // verify the payment and return response
        return new PaymentVerifyResponse('TRANSACTION_ID');
    }
}

$manager->extend('example', function () {
    return new ExampleProvider();
});


/**
 * PaymentRequestNeeds
 * you can create new PaymentRequestNeeds
 * and customize it for your own provider
 *
 * for more information read Default payment providers
 */

$requestNeeds = new PaymentRequestNeeds();
$requestNeeds->setAmount(1000);
$requestNeeds->setReturnUrl('YOUR_CALLBACK_URL');
$requestNeeds->set('attr', 'value');

// Call with provider name
// by this, you can change provider on-the-fly
$manager->provider('example')
    ->callPaymentRequest($requestNeeds);

// OR set provider as default provider
// this set example as default provider
// and you can call provider methods directly from manager
$manager->setDefaultProvider('example');


$response = $manager->callPaymentRequest($requestNeeds);


$verifyNeeds = new PaymentNeeds();
$verifyNeeds->set('amount', 1000);
$verifyNeeds->set('payment_id', 15415648);

$response = $manager->callPaymentVerify($verifyNeeds);


// Zarinpal example
$provider = $manager->provider('zarinpal');

// create payment request:
$requestNeeds = new PaymentRequestNeeds();

$requestNeeds
    ->setAmount(1000)// required
    ->setReturnUrl('http://some_url')// required
    ->set('description', 'some description')// required
    ->set('mobile', 'YOUR_PHONE_NUMBER')// optional
    ->set('email', 'YOUR_EMAIL_ADDRESS'); // optional

$response = $manager->provider('zarinpal')->callPaymentRequest($requestNeeds);

$redirectUrl = $response->getPaymentUrl(); // each  request responses has  this method

// save the Authority to database
// we need that for payment verify
$authority = $response->get('authority'); // zarinpal provider response has this filed too.

// Redirect user to payment page
header('Location: ' . $response->getPaymentUrl());

// save response $data any where, you need these for payment verify

/**
 * @var $provider \Laratalks\PaymentGateways\Providers\Soap\ZarinpalProvider
 */
$provider = $manager->provider('zarinpal');


// payment verification
if (
    strtolower($_SERVER['REQUEST_METHOD']) === 'post'
) {
    // AUTHORITY comes from payment request, response and you save it before
    $verifyNeeds = new PaymentNeeds();
    $verifyNeeds
        ->set('amount', 1000)
        ->set('authority', 'YOUR_AUTHORITY');

    $response = $provider->callVerifyRequest($verifyNeeds);

    $transactionId = $response->getTransactionId(); // each verify response has this  method
    $status = $response->get('status');   // zarinpal provider has status field too.

}


/**
 * Work with PaymentNeeds
 * every Request|Verify need extended `PaymentNeeds` class
 */

$needs = new PaymentNeeds();
$needs->has('key'); //  false
$needs->set('key', 'value');
$needs->has('key'); //  true
$needs->hasAll('key', 'attr', 'foo'); // false
$needs->set('attr', 'val');
$needs->hasAll('key', 'attr', 'foo'); // false
$needs->set('foo', 'bar');
$needs->hasAll('key', 'attr', 'foo'); // true

$value = $needs->get('key'); // value
//OR
$value = $needs->getKey(); // value
$value = $needs->getFoo(); // bar
$value = $needs->getWhat(); // null



