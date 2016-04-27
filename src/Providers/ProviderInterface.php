<?php

namespace Laratalks\PaymentGateways\Providers;

use Laratalks\PaymentGateways\ValueObjects\PaymentNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentResponse;

interface ProviderInterface
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @param PaymentRequestNeeds $needs
     * @return PaymentResponse
     */
    public function callPaymentRequest(PaymentRequestNeeds $needs);

    /**
     * @param PaymentNeeds $needs
     * @return PaymentResponse
     */
    public function callVerifyRequest(PaymentNeeds $needs);
    
}