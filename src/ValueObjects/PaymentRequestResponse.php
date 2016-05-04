<?php
namespace Laratalks\PaymentGateways\ValueObjects;

class PaymentRequestResponse extends PaymentResponse
{
    public function __construct($paymentUrl)
    {
        $this->setPaymentUrl($paymentUrl);
    }

    public function setPaymentUrl($paymentUrl)
    {
        return $this->set('payment_url', $paymentUrl);
    }

    public function getPaymentUrl()
    {
        return $this->get('payment_ur');
    }
}