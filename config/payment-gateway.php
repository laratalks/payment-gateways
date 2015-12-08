<?php

return [

    'default_provider' => '',

    'providers' => [


        'payline' => [
            'api' => '',
            'gateway_send_url' => 'http://payline.ir/payment/gateway-send',
            'gateway_verify_url' => 'http://payline.ir/payment/gateway-result-second',
            'formatted_payment_url' => "http://payline.ir/payment/gateway-%d"
        ]



    ]
];