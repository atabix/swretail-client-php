<?php 

// helper functions for SWRetail package


if (! function_exists('swconfig')) {
    function swconfig($key = null)
    {
        // TODO Make use of published config in user app.
        $config = include(dirname(__DIR__) . '/config/swretail.php');
        
        if (is_null($key)) {
            return $config;
        }
        if (array_key_exists($key,$config)) {
            return $config[$key];
        }
        return null;
    }
}
