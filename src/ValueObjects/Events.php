<?php

namespace Laratalks\PaymentGateways\ValueObjects;

class Events
{
    const REQUEST_PAYMENT_URL_BEFORE = 'request_payment_url_before';
    const REQUEST_PAYMENT_URL_AFTER = 'request_payment_url_before';
    const VERIFY_AFTER = 'verify_after';
    const VERIFY_BEFORE = 'verify_before';
}