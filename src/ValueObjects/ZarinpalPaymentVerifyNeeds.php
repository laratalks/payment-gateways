<?php
namespace Laratalks\PaymentGateways\ValueObjects;

class ZarinpalPaymentVerifyNeeds extends PaymentNeeds
{
    public function __construct($amount = null, $authority = null)
    {
        if ($amount !== null) {

            $this->setAmount($amount);
        }

        if ($authority != null) {
            $this->setAuthority($authority);
        }
    }

    public function setAuthority($authority)
    {
        $this->setCustomAttribute('authority', $authority);

        return $this;
    }

    public function setAmount($amount)
    {
        $this->setCustomAttribute('amount', $amount);

        return $this;
    }

    public function getAuthority()
    {
        return $this->getCustomAttribute('authority');
    }

    public function getAmount()
    {
        return $this->getCustomAttribute('amount');
    }


    public function isVerified()
    {
        return $this->hasAll('amount', 'authority');
    }

}