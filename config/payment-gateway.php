<?php

return [

    'default_provider' => 'zarinpal',

    'providers' => [

        'payline' => [
            'api' => '', // payline API key
            'gateway_send_url' => 'http://payline.ir/payment/gateway-send',
            'gateway_verify_url' => 'http://payline.ir/payment/gateway-result-second',
            'gateway_payment_url' => "http://payline.ir/payment/gateway-%d"
        ],

        'zarinpal' => [
            'merchant_id' => '', // Zarinpal Merchant Code
            'wsdl' => 'https://www.zarinpal.com/pg/services/WebGate/wsdl',
            'gateway_send_url' => '‫‪https://www.zarinpal.com/pg/StartPay/%s'
        ]

    ],

    'proxy' => [
        'enable' => false, // true, when you want send requests through proxy
        'type' => 'http', // available: http, https, socks5(only on non-wsdl requests)
        'use_credentials' => false, // true, if your proxy needs credentials
        'host' => 'localhost',
        'port' => 8123,
        'username' => '',
        'password' => ''
    ]
];