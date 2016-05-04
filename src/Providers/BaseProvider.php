<?php

namespace Laratalks\PaymentGateways\Providers;


use Laratalks\PaymentGateways\Exceptions\PaymentGatewayException;

abstract class BaseProvider implements ProviderInterface
{
    protected $configs;

    /**
     * BaseProvider constructor.
     * @param array $configs
     */
    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     * @throws null
     */
    protected function getProviderConfig($key, $default = null)
    {
        return array_get($this->configs['providers'][$this->getName()], $key, $default);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     * @throws null
     */
    protected function getConfig($key, $default = null)
    {
        return array_get($this->configs, $key, $default);
    }

    /**
     * @param \Exception $e
     * @param callable|null $handler
     * @return mixed
     * @throws PaymentGatewayException
     * @throws \Exception
     */
    protected function handleException(\Exception $e, callable $handler = null)
    {
        if ($e instanceof PaymentGatewayException) {
            throw $e;
        }

        if (is_callable($handler)) {
            return call_user_func($handler, $e);
        }

        throw $e;
    }

    /**
     * @param float $amount Rials
     * @return float
     */
    protected function calculateAmount($amount)
    {
        return $amount;
    }

}