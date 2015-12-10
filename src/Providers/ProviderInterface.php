<?php

namespace Laratalks\PaymentGateways\Providers;

use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentTransaction;

interface ProviderInterface
{
    /**
     * Call endpoint and get return url
     *
     * @param PaymentRequestNeeds $needs
     * @return string|mixed
     */
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs);

    /**
     * Call and verify for given payload
     *
     * @param array|\Symfony\Component\HttpFoundation\Request $payload
     * @return PaymentTransaction
     */
    public function callAndVerify($payload);

    /**
     * Get an option from config
     *
     * @param      $key
     * @param null $def
     * @return string
     */
    public function getFromConfig($key, $def = null);

    /**
     * Get gateway payment URL :)
     *
     * @return string
     */
    public function getPaymentUrl();
}