<?php

class PaymentTransactionTest extends TestCase
{
    public function test_toArray_must_return_empty_array_with_empty_values()
    {
        $paymentTransaction = new \Laratalks\PaymentGateways\ValueObjects\PaymentTransaction();

        $this->assertEquals($paymentTransaction->toArray(), []);
    }

    public function test_getters()
    {
        $paymentTransaction = new \Laratalks\PaymentGateways\ValueObjects\PaymentTransaction('trans_id', 154646, 'id_get', 154);

        $this->assertEquals($paymentTransaction->getPaymentField(), 'id_get');
        $this->assertEquals($paymentTransaction->getPaymentId(), 154);
        $this->assertEquals($paymentTransaction->getTransactionField(), 'trans_id');
        $this->assertEquals($paymentTransaction->getTransactionId(), 154646);

    }
}