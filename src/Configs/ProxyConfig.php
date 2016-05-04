<?php
namespace Laratalks\PaymentGateways\Configs;

class ProxyConfig extends BaseConfig
{
    const TYPE_HTTP = 'http';
    const TYPE_HTTPS = 'https';
    const TYPE_SOCKS5 = 'socks5';


    public function __construct()
    {
        $this
            ->setEnabled()
            ->setType()
            ->setHost()
            ->setPort()
            ->setUseCredentials()
            ->setUsername()
            ->setPassword();
    }

    public function setType($type = '')
    {
        if (in_array($type, [
            static::TYPE_HTTP,
            static::TYPE_HTTPS,
            static::TYPE_SOCKS5
        ])) {
            return $this->set('type', $type);
        }

        return $this->set('type', static::TYPE_HTTP);
    }

    public function setEnabled($enabled = false)
    {
        $this->configs['enabled'] = (bool)$enabled;

        return $this;
    }

    public function setHost($host = 'localhost')
    {
        $this->configs['host'] = $host;

        return $this;
    }

    public function setPort($port = '8080')
    {
        $this->configs['port'] = (int)$port;

        return $this;
    }

    public function setUseCredentials($useCredentials = true)
    {
        $this->configs['use_credentials'] = (bool)$useCredentials;

        return $this;
    }

    public function setUsername($username = '')
    {
        $this->configs['username'] = $username;

        return $this;
    }

    public function setPassword($password = '')
    {
        $this->configs['password'] = $password;

        return $this;
    }

}