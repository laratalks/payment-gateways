<?php

use Laratalks\PaymentGateways\GatewayManager;
use Laratalks\PaymentGateways\Providers\ProviderInterface;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;

// Provide config for each provider in a single array and pass that to the
// manager array.
$config = [

    'default_provider' => 'payline',

    'providers' => [


        'payline' => [
            'api' => '',
            'gateway_send_url' => 'http://payline.ir/payment/gateway-send',
            'gateway_verify_url' => 'http://payline.ir/payment/gateway-result-second',
            'formatted_payment_url' => "http://payline.ir/payment/gateway-%d"
        ]

    ]
];


// The facade to all abilities
$manager = new GatewayManager($config);


/**
 * Extending your own provider
 */
class ExampleProvider implements ProviderInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * @param PaymentRequestNeeds $needs
     * @return \Laratalks\PaymentGateways\ValueObjects\PaymentResponse
     */
    public function callPaymentRequest(PaymentRequestNeeds $needs)
    {
        // TODO: Implement callPaymentRequest() method.
    }

    /**
     * @param \Laratalks\PaymentGateways\ValueObjects\PaymentNeeds $needs
     * @return \Laratalks\PaymentGateways\ValueObjects\PaymentResponse
     */
    public function callVerifyRequest(\Laratalks\PaymentGateways\ValueObjects\PaymentNeeds $needs)
    {
        // TODO: Implement callVerifyRequest() method.
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


$verifyNeeds = new \Laratalks\PaymentGateways\ValueObjects\PaymentNeeds();
$verifyNeeds->set('amount', 1000);
$verifyNeeds->set('payment_id', 15415648);

$response = $manager->callPaymentVerify($verifyNeeds);


// Zarinpal example
$provider = $manager->provider('zarinpal');

// create payment request:
$requestNeeds = new \Laratalks\PaymentGateways\ValueObjects\ZarinpalPaymentRequestNeeds(1000, 'http://some_url', 'some_description');
// or
$requestNeeds = new \Laratalks\PaymentGateways\ValueObjects\ZarinpalPaymentRequestNeeds();
$requestNeeds
    ->setAmount(1000)// required
    ->setReturnUrl('http://some_url')// required
    ->setDescription('some description')// required
    ->setMobile('MOBILE_NUMBER')// optional
    ->setEmail('EMAIL_ADDRESS'); // optional

$response = $manager->provider('zarinpal')->callPaymentRequest($requestNeeds);

// save response $data any where, you need these for payment verify

/**
 * @var $provider \Laratalks\PaymentGateways\Providers\Soap\ZarinpalProvider
 */
$provider = $manager->provider('zarinpal');


// payment verification
if (
    strtolower($_SERVER['REQUEST_METHOD']) === 'post' // zarinpal send verification data using POST method
    && ZarinpalProvider::checkPaymentStatusIsOK() // check payment is OK
) {
    // AUTHORITY comes from payment request, response and you save it before
    $verifyNeeds = new \Laratalks\PaymentGateways\ValueObjects\ZarinpalPaymentVerifyNeeds(1000, 'AUTHORITY');
    // or
    $verifyNeeds = new \Laratalks\PaymentGateways\ValueObjects\ZarinpalPaymentVerifyNeeds();
    $verifyNeeds
        ->setAmount(1000)
        ->setAuthority('AUTHORITY');

    $response = $provider->callVerifyRequest($verifyNeeds);

    // $response variable contains `ref_id` and `status` properties
    $transactionId = $response->get('ref_id');
    $status = $response->get('status');

}


/**
 * Work with PaymentNeeds
 * every Request|Verify need extended `PaymentNeeds` class 
 */

$needs  = new \Laratalks\PaymentGateways\ValueObjects\PaymentNeeds();
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



