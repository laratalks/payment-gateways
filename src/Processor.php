<?php

namespace Jobinja\PaymentGateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Jobinja\PaymentGateways\Providers\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Processor
{
    /**
     * @var PaymentRequestNeeds
     */
    protected $requestNeeds;

    /**
     * @var \Jobinja\PaymentGateways\GatewayManager
     */
    protected $manager;

    /**
     * Config array
     *
     * @var array
     */
    protected $config;

    /**
     * Processor constructor.
     *
     * @param \Jobinja\PaymentGateways\GatewayManager              $manager
     * @param \Jobinja\PaymentGateways\Providers\ProviderInterface $provider
     * @param array                                                $config
     */
    public function __construct(GatewayManager $manager, ProviderInterface $provider, array $config = [])
    {
        $this->provider = $provider;
        $this->manager = $manager;
        $this->config = $config;
        $this->requestNeeds = $this->setRequestNeeds(new PaymentRequestNeeds());
    }

    /**
     * Clone and set amount for next call
     *
     * @param $amount
     * @return $this
     */
    public function withAmount($amount)
    {
        // As changing the state of current request need may cause unexpected
        // behaviour, we try to keep the immutablity.
        $obj = clone $this;
        $obj->getRequestNeeds()->setAmount($amount);
        return $obj;
    }

    /**
     * Set return url for next call
     *
     * @param $returnUrl
     * @return $this
     */
    public function withReturnUrl($returnUrl)
    {
        $obj = clone $this;
        $obj->getRequestNeeds()->setReturnUrl($returnUrl);
        return $obj;
    }

    /**
     * Set custom attribute
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function withCustomAttribute($name, $value)
    {
        $obj = clone $this;
        $obj->getRequestNeeds()->setCustomAttribute($name, $value);
        return $obj;
    }

    /**
     * Set custom attributes
     *
     * @param array $attrs
     * @return $this
     */
    public function withCustomAttributes(array $attrs = [])
    {
        $obj = clone $this;
        $obj->getRequestNeeds()->setCustomAttributes($attrs);
        return $obj;
    }

    /**
     * Get payment url
     *
     * @param \Jobinja\PaymentGateways\PaymentRequestNeeds|null $needs
     * @return mixed
     */
    public function getPaymentUrl(PaymentRequestNeeds $needs = null)
    {
        $needs = $needs ?: $this->requestNeeds;

        // These events are handled right before calling the endpoint
        // if one of the handler's returned false we will stop.
        if (!$this->runClosuresForEvent(Events::REQUEST_PAYMENT_URL_BEFORE, $needs)) {
            return false;
        }

        $result = $this->provider->callAndGetReturnUrl($needs);

        // These events are handled right after calling the endpoint
        // if one of the handler's returned false we will stop.
        if (!$this->runClosuresForEvent(Events::REQUEST_PAYMENT_URL_AFTER, $result, $needs)) {
            return false;
        }

        return $result;
    }

    /**
     * Run given closures for event
     *
     * @param $eventName
     * @return bool
     */
    protected function runClosuresForEvent($eventName)
    {
        // Remove $eventName from callable's args.
        $args = array_shift($args = func_get_args());

        foreach (array_get($this->manager->getEvents(), []) as $event) {
            // Call the listener of the event
            $res = call_user_func_array($event, $args);
            if ($res === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify from current request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return mixed
     */
    public function verify(Request $request = null)
    {
        // We use symfony request for our verifying logic
        $request = $request = $request ?: Request::createFromGlobals();

        if (!$this->runClosuresForEvent(Events::VERIFY_BEFORE, $request)) {
            return false;
        }

        $result = $this->provider->callAndVerify($request);

        if (!$this->runClosuresForEvent(Events::VERIFY_AFTER, $request)) {
            return false;
        }

        return $result;
    }

    /**
     * Get request needs
     *
     * @return \Jobinja\PaymentGateways\PaymentRequestNeeds
     */
    public function getRequestNeeds()
    {
        return $this->requestNeeds;
    }

    /**
     * Set request needs
     *
     * @param \Jobinja\PaymentGateways\PaymentRequestNeeds $needs
     * @return $this
     */
    public function setRequestNeeds(PaymentRequestNeeds $needs)
    {
        $this->requestNeeds = $needs;
        return $this;
    }

    /**
     * New request need on object clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->requestNeeds = new PaymentRequestNeeds();
    }

    /**
     * Swap on ping failure
     *
     * @param     $swapTo
     * @param int $connectTimeout
     * @param int $timeout
     * @return $this|\Jobinja\PaymentGateways\Processor
     */
    public function swapOnPingFailure($swapTo, $connectTimeout = 15, $timeout = 30)
    {
        if (!($url = $this->provider->getFromConfig('health_ping_url'))) {
            return $this;
        }

        if ($this->pingUrl($url, $connectTimeout, $timeout)) {
            return $this;
        }

        return $this->manager->provider($swapTo);
    }

    /**
     * Ping url
     *
     * @param $connectTimeout
     * @param $timeout
     * @param $url
     * @return bool
     */
    protected function pingUrl($url, $connectTimeout, $timeout)
    {
        try {
            $client = new Client();
            $client->get($url, [
                RequestOptions::CONNECT_TIMEOUT => $connectTimeout,
                RequestOptions::TIMEOUT         => $timeout
            ]);
            return true;
        } catch (ServerException $e) {
            return false;
        } catch (ClientException $e) {
            return true;
        } catch (ConnectException $e) {
            return false;
        }
    }
}