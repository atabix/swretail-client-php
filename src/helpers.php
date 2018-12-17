<?php

namespace SWRetail;

// helper functions for SWRetail package

use SWRetail\Models\Type\Percentage;
use SWRetail\Models\Type\Price;

if (! \function_exists('swconfig')) {
    function swconfig($key = null)
    {
        // TODO Make use of published config in user app.
        $config = include \dirname(__DIR__) . '/config/swretail.php';

        if (\is_null($key)) {
            return $config;
        }
        if (\array_key_exists($key, $config)) {
            return $config[$key];
        }

        return null;
    }
}

function snake_case($name)
{
    return \strtolower(\preg_replace('/(.)(?=[A-Z])/', '$1_', $name));
}

function price_or_percentage($value)
{
    if ($value instanceof Price || $value instanceof Percentage) {
        return $value;
    }

    return (\substr($value, -1, 1) == '%')
        ? new Percentage(\substr($value, 0, \strlen($value) - 1))
        : new Price($value);
}
