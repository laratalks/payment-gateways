<?php

namespace Jobinja\PaymentGateways;

use Jobinja\PaymentGateways\Providers\ProviderInterface;

interface GatewayFactoryInterface
{
    /**
     * Register a new provider
     *
     * @param          $providerName
     * @param \Closure $builder
     * @return void
     */
    public function extend($providerName, \Closure $builder);

    /**
     * Get provider for given name
     *
     * @param null $name
     * @return ProviderInterface
     */
    public function provider($name = null);

    /**
     * Get default provider
     *
     * @return string
     */
    public function getDefaultProvider();
}