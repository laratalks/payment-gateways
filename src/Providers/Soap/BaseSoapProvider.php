<?php

namespace Jobinja\PaymentGateways\Providers\Soap;

use Jobinja\PaymentGateways\PaymentRequestNeeds;
use Jobinja\PaymentGateways\Providers\BaseProvider;
use SoapClient;

abstract class BaseSoapProvider extends BaseProvider implements SoapProviderInterface
{
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
     * Call endpoint and get response.
     *
     * @param \Jobinja\PaymentGateways\PaymentRequestNeeds $needs
     * @return mixed
     */
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs)
    {
        $soap = $this->buildSoap($this->getEndpoint(), $this->getOptions());
        $result = $soap->{$this->getRequestMethodName()}($this->serialize($needs));
        return $result;
    }

    /**
     * Serialize payment request needs.
     *
     * @param \Jobinja\PaymentGateways\PaymentRequestNeeds $needs
     * @return mixed
     */
    protected abstract function serialize(PaymentRequestNeeds $needs);

    /**
     * Build soap client.
     *
     * @param       $endpoint
     * @param array $options
     * @return \SoapClient
     */
    protected function buildSoap($endpoint, $options = [])
    {
        return new SoapClient($endpoint, $options);
    }

    /**
     * Call and verify based on current request
     */
    public function callAndVerify()
    {
        // TODO: Implement callAndVerify() method.
    }

    /**
     * Get endpoint address
     */
    public function getEndpoint()
    {
        // TODO: Implement getEndpoint() method.
    }

    /**
     * Get reque
     */
    public function getRequestMethodName()
    {
        // TODO: Implement getRequestMethodName() method.
    }

    public function getVerifyMethodName()
    {
        // TODO: Implement getVerifyMethodName() method.
    }
}