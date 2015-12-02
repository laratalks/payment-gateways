<?php

namespace Laratalks\PaymentGateways;

class VerifyResponse
{
    protected $gateway;
    protected $original;
    protected $isOk;
    protected $verified;

    public function __construct(array $original, $gateway, $isOk, $verified, $alreadyVerified)
    {
        $this->gateway = (string) $gateway;
        $this->original = $original;
        $this->isOk = (bool) $isOk;
        $this->verified = (bool) $verified;
        $this->alreadyVerified = (bool) $alreadyVerified;
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public function getGateway()
    {
        return (string) $this->gateway;
    }

    public function isOk()
    {
        return (bool) $this->isOk;
    }

    public function verified()
    {
        return (bool) $this->verified;
    }
}