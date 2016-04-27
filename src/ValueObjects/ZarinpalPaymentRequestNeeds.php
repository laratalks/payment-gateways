<?php
namespace Laratalks\PaymentGateways\ValueObjects;

use Laratalks\PaymentGateways\Exceptions\InvalidArgumentException;
use Laratalks\PaymentGateways\Exceptions\InvalidPaymentNeedsException;

class ZarinpalPaymentRequestNeeds extends PaymentRequestNeeds
{
    public function __construct($amount = null, $returnUrl = null, $description = null)
    {
        if ($description !== null) {
            $this->setDescription($description);
        }
        
        parent::__construct($amount, $returnUrl);
    }

    public function setDescription($description)
    {
        $this->setCustomAttribute('description', $description);

        return $this;
    }

    public function getDescription()
    {
        if (!$this->has('description')) {
            throw new InvalidPaymentNeedsException;
        }

        
        return $this->getCustomAttribute('description');
    }

    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Valid email required');
        }

        $this->setCustomAttribute('email', $email);

        return $this;
    }

    public function getEmail()
    {
        return $this->getCustomAttribute('email');
    }

    public function setMobile($mobile)
    {
        $this->setCustomAttribute('mobile', $mobile);
        
        
        return $this;
    }

    public function getMobile()
    {
        return $this->getCustomAttribute('mobile');
    }


    public function isVerified()
    {
        return parent::isVerified() && $this->has('description');
    }

}