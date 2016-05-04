<?php
namespace Laratalks\PaymentGateways\ValueObjects;

class PaymentVerifyResponse extends PaymentResponse
{
    public function __construct($transactionId)
    {
        $this->setTransactionId($transactionId);
    }

    public function getTransactionId()
    {
        return $this->get('transaction_id');
    }

    public function setTransactionId($transId)
    {
        return $this->set('transaction_id', $transId);
    }
}