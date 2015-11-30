<?php

namespace Jobinja\PaymentGateways;

interface GatewayFactoryInterface
{
    public function extend($driverName, \Closure $builder);

    public function provider($name = null);

    public function getDefaultProvider();
}