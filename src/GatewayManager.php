<?php

namespace Laratalks\PaymentGateways;

class GatewayManager implements GatewayFactoryInterface
{
    /**
     * Available providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * On events
     *
     * @var array
     */
    protected $events = [];

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

        if ($this->defaultProvider = array_get($this->config, 'provider') === null) {
            throw new \InvalidArgumentException('No default provider given, try setting "provider" on config array.');
        }
    }

    /**
     * Register a new provider
     *
     * @param          $providerName
     * @param \Closure $builder
     */
    public function extend($providerName, \Closure $builder)
    {
        $this->customs[$providerName] = $builder;
    }

    /**
     * Get provider for given name.
     *
     * @param null $name
     * @return Processor
     */
    public function provider($name = null)
    {
        $name = $name ?: $this->getDefaultProvider();

        if (!isset($this->providers[$name])) {
            $this->providers[$name] = new Processor($this, $this->providers[$name], $this->config);
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
        $method = 'call' . ucfirst($what) . 'Creator';

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new \InvalidArgumentException('No creator found for "' . $what . '" payment gateway provider."');
    }

    /**
     * Call from provider
     *
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments = [])
    {
        return call_user_func_array([$this->provider(), $name], $arguments);
    }

    /**
     * on event resolver
     *
     * @param          $event
     * @param \Closure $resolver
     */
    public function on($event, \Closure $resolver)
    {
        $this->events[$event][] = $resolver;
    }

    /**
     * Get after events
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }
}