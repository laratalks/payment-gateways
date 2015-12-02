<?php

namespace Laratalks\PaymentGateways\Providers\Soap;

use Laratalks\PaymentGateways\Exceptions\InvalidArgumentException;
use Laratalks\PaymentGateways\PaymentRequestNeeds;
use Laratalks\PaymentGateways\PaymentRequestResponse;
use Laratalks\PaymentGateways\Providers\BaseProvider;
use Laratalks\PaymentGateways\VerifyResponse;
use SoapClient;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseSoapProvider extends BaseProvider implements SoapProviderInterface
{
    /**
     * Config array
     *
     * @var array
     */
    protected $config = [];

    /**
     * BaseSoapProvider constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Call endpoint and get response.
     *
     * @param \Laratalks\PaymentGateways\PaymentRequestNeeds $needs
     * @return mixed
     */
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs)
    {
        $soap = $this->buildSoap($this->getWsdl(), $this->getOptions());
        $result = $soap->{$this->getRequestMethodName()}($this->serializePaymentRequest($needs));
        return $this->handleRequestResponse($result)->getReturnUrl();
    }

    /**
     * Call and verify given request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Laratalks\PaymentGateways\VerifyResponse
     */
    public function callAndVerify(Request $request)
    {
        $soap = $this->buildSoap($this->getWsdl(), $this->getOptions());
        $result = $soap->{$this->getVerifyMethodName()}($this->serializeVerify($request));
        return $this->handleVerifyResponse($result)->isOk();
    }

    /**
     * Get request method name
     *
     * @return string
     */
    public function getRequestMethodName()
    {
        $methodName = array_get($this->config, 'request_method_name', null);
        if (null === $methodName) {
            throw new InvalidArgumentException("No request method name set in config.");
        }
        return $methodName;
    }

    /**
     * Get verify method name
     *
     * @return string
     */
    public function getVerifyMethodName()
    {
        $methodName = array_get($this->config, 'verify_method_name', null);
        if (null === $methodName) {
            throw new InvalidArgumentException('No verify method name set in config.');
        }
        return $methodName;
    }

    /**
     * Get wsdl endpoint
     *
     * @return string
     */
    public function getWsdl()
    {
        $wsdl = array_get($this->config, 'wsdl', null);
        if (null == $wsdl) {
            throw new InvalidArgumentException('No wsdl endpoint given at config.');
        }
        return $wsdl;
    }

    /**
     * Default options for soap.
     *
     * @return array
     */
    protected function getOptions()
    {
        return ['encoding' => 'utf-8'];
    }

    /**
     * Build soap client
     *
     * @param       $wsdl
     * @param array $options
     * @return \SoapClient
     */
    protected function buildSoap($wsdl, array $options = [])
    {
        return new SoapClient($wsdl, $options);
    }

    /**
     * Handle request response
     *
     * @param \stdClass $result
     * @return PaymentRequestResponse
     */
    protected abstract function handleRequestResponse(\stdClass $result);

    /**
     * Handle verify response
     *
     * @param \stdClass $result
     * @return VerifyResponse
     */
    protected abstract function handleVerifyResponse(\stdClass $result);

    /**
     * Get an array from the needs.
     *
     * @param \Laratalks\PaymentGateways\PaymentRequestNeeds $needs
     * @return array
     */
    protected abstract function serializePaymentRequest(PaymentRequestNeeds $needs);

    /**
     * Serialize symfony request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    protected abstract function serializeVerify(Request $request);
}