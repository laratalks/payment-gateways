<?php
namespace Laratalks\PaymentGateways\Configs;

class ProviderConfig extends BaseConfig
{
    protected $configs;
    protected $name;

    public function __construct($name, $apiKey = '')
    {
        $this
            ->setName($name)
            ->setApiKey($apiKey);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setApiKey($apiKey = '')
    {
        $this->configs['api_key'] = $apiKey;

        return $this;
    }
}