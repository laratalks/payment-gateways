<?php

namespace Jobinja\PaymentGateways\Providers;

use Jobinja\PaymentGateways\PaymentRequestNeeds;
use Symfony\Component\HttpFoundation\Request;

interface ProviderInterface
{
    /**
     * Call endpoint and get return url
     *
     * @param \Jobinja\PaymentGateways\PaymentRequestNeeds $needs
     * @return string
     */
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs);

    /**
     * Call and verify for given symfony request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return string
     */
    public function callAndVerify(Request $request);

    /**
     * Get an option from config
     *
     * @param      $key
     * @param null $def
     * @return string
     */
    public function getFromConfig($key, $def = null);
}