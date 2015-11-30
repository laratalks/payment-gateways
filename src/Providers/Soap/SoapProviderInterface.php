<?php

namespace Jobinja\PaymentGateways\Providers\Soap;

use Jobinja\PaymentGateways\Providers\ProviderInterface;

interface SoapProviderInterface extends ProviderInterface
{
    public function getEndpoint();
    public function getRequestMethodName();
    public function getVerifyMethodName();
}