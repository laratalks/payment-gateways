<?php

namespace Laratalks\PaymentGateways\Providers;

use Laratalks\PaymentGateways\PaymentRequestNeeds;
use Symfony\Component\HttpFoundation\Request;

interface ProviderInterface
{
    /**
     * Call endpoint and get return url
     *
     * @param \Laratalks\PaymentGateways\PaymentRequestNeeds $needs
     * @return string
     */
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs);

    /**
     * Call and verify for given payload
     *
     * @param array|\Symfony\Component\HttpFoundation\Request $payload
     * @return string
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