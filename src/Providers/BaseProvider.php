<?php

namespace Laratalks\PaymentGateways\Providers;

use Laratalks\PaymentGateways\PaymentRequestNeeds;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseProvider implements ProviderInterface
{
    /**
     * Get an option from config
     *
     * @param      $key
     * @param null $def
     * @return string
     */
    public function getFromConfig($key, $def = null)
    {
        return array_get($this->config, $key, $def);
    }

    /**
     * Handle request response
     *
     * @param $result
     * @return PaymentRequestResponse
     */
    protected abstract function handleRequestResponse($result);

    /**
     * Handle verify response
     *
     * @param $result
     * @return VerifyResponse
     */
    protected abstract function handleVerifyResponse($result);

    /**
     * Get an array from the needs.
     *
     * @param PaymentRequestNeeds $needs
     * @return array
     */
    protected abstract function serializePaymentRequest(PaymentRequestNeeds $needs);

    /**
     * Serialize Verify request payload
     *
     * @param array|Request $payload
     * @return array
     */
    protected abstract function serializeVerify($payload);
}