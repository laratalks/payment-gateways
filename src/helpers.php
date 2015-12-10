<?php

if (function_exists('array_get')) {
    /**
     * Array get
     *
     * @param array $array
     * @param  $key
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    function array_get(array $array, $key, $default = null)
    {
        if ($default instanceof \Exception) {
            throw $default;    
        }
        
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return resolve_value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('resolve_value')) {
    /**
     * Resolve value
     *
     * @param $value
     * @return bool
     */
    function resolve_value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}


if (!function_exists('studly_case')) {
    /**
     * Make an string studly.
     *
     * @param $string
     * @return string
     */
    function studly_case($string)
    {
        static $cache = [];
        if (isset($cache[$string])) {
            return $cache[$string];
        }

        return $cache[$string] = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $string)));
    }
}


if (! function_exists('redirect_url')) {

    /**
     * Redirect agent to another url
     * @param $url
     */
    function redirect_url($url) {
        header('Location: ' . $url, true);
        die;
    }
}
