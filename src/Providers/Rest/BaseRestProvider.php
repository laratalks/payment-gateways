<?php

namespace Laratalks\PaymentGateways\Providers\Rest;

use GuzzleHttp\Client;
use Laratalks\PaymentGateways\Providers\BaseProvider;

abstract class BaseRestProvider extends BaseProvider
{
    public function getClient(array $config = [])
    {
        // set proxy if enabled
        if ($this->getConfig('proxy.enable') === true) {
            $config['proxy'][$this->getConfig('proxy.type')] = sprintf(
                "tcp://%s:%d",
                $this->getConfig('proxy.host'),
                $this->getConfig('proxy.port')
            );

            if ($this->getConfig('proxy.use_credentials') === true) {
                $config['proxy'][$this->getConfig('proxy.type')] = sprintf(
                    "tcp://%s:%s@%s:%d",
                    $this->getConfig('proxy.username'),
                    $this->getConfig('proxy.password'),
                    $this->getConfig('proxy.host'),
                    $this->getConfig('proxy.port')
                );
            }
        }

        return new Client($config);
    }


}