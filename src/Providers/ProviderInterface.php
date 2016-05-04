<?php

namespace Laratalks\PaymentGateways\Providers;

use Laratalks\PaymentGateways\ValueObjects\PaymentNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestResponse;
use Laratalks\PaymentGateways\ValueObjects\PaymentVerifyResponse;
use Symfony\Component\HttpFoundation\Request;

interface ProviderInterface
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @param PaymentRequestNeeds $needs
     * @return PaymentRequestResponse
     */
    public function callPaymentRequest(PaymentRequestNeeds $needs);

    /**
     * @param PaymentNeeds $needs
     * @param Request $request
     * @return PaymentVerifyResponse
     */
    public function callVerifyRequest(PaymentNeeds $needs, Request $request = null);
    
}