<?php
namespace Laratalks\PaymentGateways\Configs;

class Config  extends BaseConfig
{
    public function setDefaultProvider($defaultProvider)
    {
        return $this->set('default_provider', $defaultProvider);
    }

    /**
     * @param ProviderConfig $providerConfig
     * @return $this
     */
    public function addProvider(ProviderConfig $providerConfig)
    {
        $this->configs['providers'][$providerConfig->getName()] = $providerConfig->toArray();
        
        return $this;
    }
    
    public function setProxy(ProxyConfig $proxy)
    {
        return $this->set('proxy', $proxy->toArray());
    }
    
}