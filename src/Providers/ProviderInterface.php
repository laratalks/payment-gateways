<?php

namespace Jobinja\PaymentGateways\Providers;

use Jobinja\PaymentGateways\PaymentRequestNeeds;

interface ProviderInterface
{
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs);
    public function callAndVerify();
}