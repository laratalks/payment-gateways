<?php

namespace Jobinja\PaymentGateways;

class PaymentRequestNeeds
{
    protected $amount;
    protected $returnUrl;
    protected $customAttrs = [];

    public function __construct($amount = null, $returnUrl = null, array $customAttrs = [])
    {
        if (null !== $amount) {
            $this->setAmount($amount);
        }

        if (null !== $returnUrl) {
            $this->setReturnUrl($returnUrl);
        }

        $this->setCustomAttributes($customAttrs);
    }

    public function setAmount($amount)
    {
        $amount = (int) $amount;
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount can not be less or equal to zero.');
        }

        $this->amount = $amount;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function setReturnUrl($url)
    {
        $this->returnUrl = (string) $url;
    }

    public function setCustomAttributes(array $attrs)
    {
        $this->customAttrs = $attrs;
    }

    public function setCustomAttribute($attr, $value)
    {
        $this->customAttrs[$attr] = $value;
        return $this;
    }

    public function getCustomAttribute($key, $default = null)
    {
        if (null === $key) {
            return $this->customAttrs;
        }

        return array_get($this->customAttrs, $key, resolve_value($default));
    }
}