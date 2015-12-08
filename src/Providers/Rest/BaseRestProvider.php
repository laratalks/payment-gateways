<?php

namespace Laratalks\PaymentGateways\Providers\Rest;

use GuzzleHttp\Client;
use Laratalks\PaymentGateways\PaymentRequestNeeds;
use Laratalks\PaymentGateways\Providers\BaseProvider;

abstract class BaseRestProvider extends BaseProvider
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function getHttpClient(array $config = [])
    {
        return new Client($config);
    }



}