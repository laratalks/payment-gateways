<?php

use Laratalks\PaymentGateways\Events;
use Laratalks\PaymentGateways\GatewayManager;
use Laratalks\PaymentGateways\PaymentRequestNeeds;
use Laratalks\PaymentGateways\Providers\Soap\ZarinpalProvider;

// Provide config for each provider in a single array and pass that to the
// manager array.
$config = [
    'default' => 'zarinpal_soap',
    'zarinpal_soap' => [
        'MerchantID' => '123456'
    ]
];

// The facade to all abilities
$manager = new GatewayManager($config);

// Register a custom gateway provider
$manager->extend('zarinpal_soap', function ($config) {
    return new ZarinpalProvider(array_get($config, 'zarinpal_soap', []));
});

// Listen on action events
// Useful for frameworks and CMS-es which allows overriding
// behaviours by changing state of objects.
$manager->on(Events::REQUEST_PAYMENT_URL_BEFORE, function (PaymentRequestNeeds $needs) {
    $needs->setAmount(500);
});

// Hook the events from calls
// you can log request coming from your provider or whatever
$manager->on(Events::VERIFY_BEFORE, function ($request) {
    // Log request etc...
});

// Get a custom provider
// Swap provider if it is not healthy by pinging a test url
// if it fails in given time periods for "connect" and "download"
$provider = $manager->provider('zarinpal_soap')
    ->swapOnPingFailure('payline_rest', 5, 10);

// Set order specs and get the right payment redirect url
// from the endpoint
$paymentUrl = $provider->withAmount(50000)
        ->withReturnUrl('http://jobinja.ir/handle_payments?order_id=TysuR&trans_id=Ousdi')
        ->withCustomAttribute('Email', 'reza@jobinja.ir')
        ->getPaymentUrl();


/** WHEN PROVIDER CALLS YOU */
$result = $provider->verify();