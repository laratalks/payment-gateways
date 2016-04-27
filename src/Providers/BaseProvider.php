<?php

namespace Laratalks\PaymentGateways\Providers;


abstract class BaseProvider implements ProviderInterface
{
    protected $configs;
    
    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    
    protected function getProviderConfig($key, $default = null)
    {
        return array_get($this->configs['providers'][$this->getName()], $key, $default);
    }
    
    
    protected function getConfig($key, $default = null)
    {
        return array_get($this->configs, $key, $default);
    }
    
    
    
}