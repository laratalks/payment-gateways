<?php

namespace Jobinja\PaymentGateways;

class GatewayManager implements GatewayFactoryInterface
{
    /**
     * Available providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Default provider.
     *
     * @var string
     */
    protected $defaultProvider;

    /**
     * Custom not set providers.
     *
     * @var array
     */
    protected $customs = [];

    /**
     * GatewayManager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        if ($defaultProvider = array_get($this->config, 'provider') === null) {
            throw new \InvalidArgumentException('No default provider given, try setting "provider" on config array.');
        }
    }

    /**
     * @param          $driverName
     * @param \Closure $builder
     */
    public function extend($driverName, \Closure $builder)
    {
        $this->customs[$driverName] = $builder;
    }

    /**
     * Get provider for given name.
     *
     * @param null $name
     * @return mixed
     */
    public function provider($name = null)
    {
        $name = $name ?: $this->getDefaultProvider();

        if (!isset($this->providers[$name])) {
            $this->providers[$name] = $this->make($name);
        }

        return $this->providers[$name];
    }

    /**
     * Get default driver.
     *
     * @return string
     */
    public function getDefaultProvider()
    {
        return $this->defaultProvider;
    }

    /**
     * Make
     *
     * @param $what
     * @return mixed
     */
    protected function make($what)
    {
        if (isset($this->customs[$what])) {
            $closure = $this->customs[$what];
            return $closure(array_get($this->config, $what, []));
        }

        return $this->callInsideCreator($what);
    }

    /**
     * Call inside creator
     *
     * @param $what
     * @return mixed
     */
    protected function callInsideCreator($what)
    {
        $method = 'call'.ucfirst($what).'Creator';

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new \InvalidArgumentException('No creator found for "'.$what.'" payment gateway provider."');
    }
}