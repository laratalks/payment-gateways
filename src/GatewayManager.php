<?php

namespace Laratalks\PaymentGateways;

use Laratalks\PaymentGateways\Configs\Config;
use Laratalks\PaymentGateways\Exceptions\InvalidProviderException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayException;
use Laratalks\PaymentGateways\Providers\ProviderInterface;
use Laratalks\PaymentGateways\Providers\Rest\UpalProvider;
use Laratalks\PaymentGateways\Providers\Soap\ZarinpalProvider;

class GatewayManager implements GatewayFactoryInterface
{

    /**
     * Default payment provider
     * @var
     */
    protected $defaultProvider;

    /**
     * All available providers list
     * @var array
     */
    protected $providers = [];

    /**
     * PaymentGateway configs
     * @var array
     */
    protected $configs;

    public function __construct(Config $config)
    {
        $this->setConfigs($config->toArray());
        $this->setDefaultProvider($this->configs['default_provider']);
    }

    /**
     * @param array $configs
     * @throws PaymentGatewayException
     */
    public function setConfigs(array $configs)
    {
        if (empty($configs)) {
            throw new PaymentGatewayException();
        }

        $this->configs = $configs;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Register a new provider
     *
     * @param $providerName
     * @param \Closure $builder
     * @return $this|void
     * @throws InvalidProviderException
     */
    public function extend($providerName, \Closure $builder)
    {
        if (isset($this->providers[$providerName])) {
            throw new InvalidProviderException;
        }

        $provider = call_user_func($builder);

        if (!$provider instanceof ProviderInterface) {
            throw new InvalidProviderException;
        }


        $this->providers[$providerName] = $provider;


        return $this;
    }

    /**
     * Get provider for given name
     *
     * @param null $name
     * @return ProviderInterface
     */
    public function provider($name = null)
    {
        return isset($this->providers[$name]) ? $this->providers[$name] : null;
    }

    /**
     * Get default provider
     *
     * @return ProviderInterface
     */
    public function getDefaultProvider()
    {
        return $this->defaultProvider;
    }

    public function setDefaultProvider($providerName)
    {
        if (!isset($this->providers[$providerName])) {
            $this->defaultProvider = $this->createProvider($providerName);
        } else {
            $this->defaultProvider = $this->providers[$providerName];
        }


        return $this;
    }

    /**
     * @param $name
     * @return ProviderInterface
     */
    protected function createProvider($name)
    {
        switch ($name) {
            case 'zarinpal':
                return new ZarinpalProvider($this->getConfigs());
            case 'upal':
                return new UpalProvider($this->getConfigs());
            default:
                return null;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (
            !method_exists(__CLASS__, $name)
            && $this->getDefaultProvider() instanceof ProviderInterface
            && method_exists($this->getDefaultProvider(), $name) 
        ) {

            return call_user_func_array([$this->getDefaultProvider(), $name], $arguments);
        }
    }
    
}