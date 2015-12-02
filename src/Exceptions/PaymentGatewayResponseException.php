<?php

namespace Laratalks\PaymentGateways\Exceptions;

class PaymentGatewayResponseException extends \Exception implements PaymentGatewayExceptionInterface
{
    protected $gateway;
    protected $result;

    public function __construct($result, $gateway, $message, $code, \Exception $previous)
    {
        $this->gateway = $gateway;
        $this->result = $result;
        parent::__construct($message, $code, $previous);
    }

    public function getGateway()
    {
        return $this->gateway;
    }

    public function getResult()
    {
        return $this->result;
    }
}