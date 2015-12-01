<?php

namespace Jobinja\PaymentGateways;

class PaymentRequestResponse
{
    protected $original;
    protected $gateway;
    protected $isOk;
    protected $returnUrl;

    public function __construct(array $original, $gateway, $isOk, $returnUrl)
    {
        $this->original = $original;
        $this->gateway = (string) $gateway;
        $this->isOk = (bool) $isOk;
        $this->returnUrl = (string) $returnUrl;
    }

    public function isOk()
    {
        return $this->isOk;
    }

    public function getGateway()
    {
        return $this->getGateway();
    }

    public function getReturnUrl()
    {
        return $this->getReturnUrl();
    }
}