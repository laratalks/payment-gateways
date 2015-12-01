<?php

namespace Jobinja\PaymentGateways\Providers;

use Jobinja\PaymentGateways\PaymentRequestNeeds;
use Symfony\Component\HttpFoundation\Request;

interface ProviderInterface
{
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs);
    public function callAndVerify(Request $request);
    public function getFromConfig($key);
}