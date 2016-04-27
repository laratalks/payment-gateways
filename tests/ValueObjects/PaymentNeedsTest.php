<?php

class PaymentNeedsTest extends TestCase
{
    public function testCall()
    {
        $paymentNeeds = new \Laratalks\PaymentGateways\ValueObjects\PaymentNeeds();
        $paymentNeeds->setCustomAttribute('amount', 1000);

        $this->assertEquals($paymentNeeds->getAmount(), 1000);


        $paymentNeeds = new \Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds(1000, null);

        $this->assertEquals($paymentNeeds->getAmount(), 1000);
    }
}