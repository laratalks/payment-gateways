<?php
namespace Laratalks\PaymentGateways\ValueObjects;


use Laratalks\PaymentGateways\Exceptions\PaymentGatewayException;

class PaymentNeeds
{
    protected $attributes = [];
    
    public function setCustomAttribute($attr, $value)
    {
        $this->attributes[strtolower($attr)] = $value;


        return $this;
    }

    public function getCustomAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, resolve_value($default));
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

    public function isVerified()
    {
        return true;
    }

    public function __call($methodName, $args)
    {

        if (method_exists($this, $methodName)) {
            return $this->$methodName($args);
        }


        if (preg_match('~^(get)([A-Z])(.*)$~', $methodName, $matches)) {

            $property = strtolower($matches[2]) . $matches[3];

            if ($matches[1] === 'get' && $this->has($property)) {
                return $this->getCustomAttribute($property);
            }
        }


        // @TODO : create Bad Method call exception
        throw new PaymentGatewayException;

    }

    public function get($key, $default = null)
    {
        return $this->getCustomAttribute($key, $default);
    }

    public function set($key, $value)
    {
        return $this->setCustomAttribute($key, $value);
    }
}