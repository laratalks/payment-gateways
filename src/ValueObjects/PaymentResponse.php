<?php

namespace Laratalks\PaymentGateways\ValueObjects;


class PaymentResponse
{
    protected $attributes = [];

    public function get($key, $default = null)
    {
        return array_get($this->attributes, strtolower($key), $default);
    }

    public function set($key, $value)
    {
        $this->attributes[strtolower($key)] = $value;

        return $this;
    }

    public function has($key)
    {
        return isset($this->attributes[strtolower($key)]);
    }

    public function hasAll($keys)
    {
        if (!is_array($keys)) {
            $keys = func_get_args();
        }

        foreach ($keys as $key) {
            if ($this->has($key) === false) {
                return false;
            }
        }

        return true;
    }
}