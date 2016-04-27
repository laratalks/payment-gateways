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
     * Get wsdl endpoint
     *
     * @return string
     */
    public function getWsdl()
    {
        $wsdl = $this->getProviderConfig('wsdl');
        
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
        $options = ['encoding' => 'utf-8'];

        if ($this->getConfig('proxy.enable') === true) {
            $options['proxy_host'] = $this->getConfig('proxy.host');
            $options['proxy_port'] = $this->getConfig('proxy.port');

            if ($this->getConfig('proxy.use_credentials') === true) {
                $options['proxy_login'] = $this->getConfig('proxy.username');
                $options['proxy_password'] = $this->getConfig('proxy.password');
            }
        }


        return $options;
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
    
    public function getClient()
    {
        return $this->buildSoap($this->getWsdl(), $this->getOptions());
    }
}