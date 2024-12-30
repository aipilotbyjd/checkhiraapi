<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('logError')) {
    function logError($controller, $method, $message, $data = [])
    {
        Log::error($controller . '::' . $method . ' - ' . $message, $data);
    }
}
