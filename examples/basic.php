<?php

use Jobinja\PaymentGateways\GatewayManager;
use Jobinja\PaymentGateways\PaymentRequestNeeds;
use Jobinja\PaymentGateways\Providers\Soap\ZarinpalProvider;

$config = [
    'provider' => 'zarinpal_soap',
    'zarinpal_soap' => [
        'MerchantID' => '123456'
    ]
];


$manager = new GatewayManager($config);

$manager->extend('zarinpal_soap', function ($config) {
    return new ZarinpalProvider(array_get($config, 'zarinpal_soap', []));
});

$requestNeeds = new PaymentRequestNeeds();
$requestNeeds->setAmount(1000);
$requestNeeds->setReturnUrl('http://localhost:8000/handle_return?order_id=12323');
$requestNeeds->setCustomAttribute('Email', 'email@123.com');
$requestNeeds->setCustomAttribute('Mobile', '091222');
$requestNeeds->setCustomAttribute('Description', 'This is description');


$provider = $manager->provider('zarinpal_soap');
$returnUrl = $provider->callAndGetReturnUrl($requestNeeds);