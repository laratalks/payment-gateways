<?php

namespace Laratalks\PaymentGateways\ValueObjects;

use Laratalks\PaymentGateways\Exceptions\InvalidArgumentException;
use Laratalks\PaymentGateways\Exceptions\InvalidPaymentNeedsException;

class PaymentRequestNeeds extends PaymentNeeds
{
    
    public function __construct($amount = null, $returnUrl = null)
    {
        if (null !== $amount) {
            $this->setAmount($amount);
        }

        if (null !== $returnUrl) {
            $this->setReturnUrl($returnUrl);
        }
    }

    public function setAmount($amount)
    {
        $amount = (int) $amount;
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount can not be less or equal to zero.');
        }

        $this->setCustomAttribute('amount', $amount);


        return $this;
    }

    public function getAmount()
    {
        if (!$this->has('amount')) {
            throw new InvalidPaymentNeedsException('Amount required.');
        }

        return $this->getCustomAttribute('amount');
    }

    public function getReturnUrl()
    {
        if (!$this->has('return_url')) {
            throw new InvalidPaymentNeedsException('Return Url required.');
        }

        return $this->getCustomAttribute('amount');
    }

    public function setReturnUrl($url)
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('return url is not valid');
        }

        $this->setCustomAttribute('return_url', (string) $url);

        return $this;
    }
    
    public function isVerified()
    {
        return $this->hasAll('amount', 'return_url');
    }


}