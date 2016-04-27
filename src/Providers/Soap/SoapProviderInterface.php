<?php

namespace Laratalks\PaymentGateways\Providers\Soap;

use Laratalks\PaymentGateways\Providers\ProviderInterface;

interface SoapProviderInterface extends ProviderInterface
{
    /**
     * Get wsdl endpoint
     *
     * @return string
     */
    public function getWsdl();
    
}