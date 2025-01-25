<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Controllers\BaseController;
use App\Models\PaymentSource;

class HomeController extends BaseController
{
    public function settings()
    {
        $paymentSources = PaymentSource::select('id', 'name', 'icon')->get();

        $settings = [
            'app_name' => 'Hirabook',
            'app_logo' => 'https://hirabook.com/logo.png',
            'app_icon' => 'https://hirabook.com/icon.png',
            'app_description' => 'Hirabook is a platform for booking services',
            'app_version' => '1.0.0',
            'app_copyright' => 'Hirabook',
            'app_email' => 'contact@hirabook.icu',
            'app_address' => 'Addis Ababa, Ethiopia',
            'app_phone' => '+251912345678',
            'app_payment_sources' => $paymentSources,
        ];

        return $this->sendResponse($settings, 'Settings fetched successfully');
    }
}

