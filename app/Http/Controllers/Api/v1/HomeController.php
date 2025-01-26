<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\BaseController;
use App\Models\PaymentSource;
use App\Models\Notification;

class HomeController extends BaseController
{
    public function settings()
    {
        try {
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
        } catch (\Exception $e) {
            logError('HomeController', 'settings', $e->getMessage());
            return $this->sendError('Error fetching settings', [], 500);
        }
    }

    public function notifications()
    {
        try {
            $perPage = request()->query('per_page', 10);
            $notifications = Notification::select('id', 'title', 'description', 'image', 'link', 'link_text', 'link_icon', 'link_color', 'created_at')
                ->latest()
                ->paginate($perPage);

            return $this->sendResponse($notifications, 'Notifications fetched successfully');
        } catch (\Exception $e) {
            logError('HomeController', 'notifications', $e->getMessage());
            return $this->sendError('Error fetching notifications', [], 500);
        }
    }
}

