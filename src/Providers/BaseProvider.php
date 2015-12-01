<?php

namespace Jobinja\PaymentGateways\Providers;

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
}