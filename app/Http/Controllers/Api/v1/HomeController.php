<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\BaseController;
use App\Models\PaymentSource;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Work;

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
            $notifications = Notification::select('id', 'title', 'description', 'image', 'is_read', 'link', 'link_text', 'link_icon', 'link_color', 'created_at')
                ->latest()
                ->paginate($perPage);

            return $this->sendResponse($notifications, 'Notifications fetched successfully');
        } catch (\Exception $e) {
            logError('HomeController', 'notifications', $e->getMessage());
            return $this->sendError('Error fetching notifications', [], 500);
        }
    }

    public function readNotification($id, $isRead = true)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->is_read = $isRead == 'true' ? true : false;
            $notification->save();
            return $this->sendResponse($notification, 'Notification read successfully');
        } catch (\Exception $e) {
            logError('HomeController', 'readNotification', $e->getMessage());
            return $this->sendError('Error reading notification', [], 500);
        }
    }

    public function readAllNotifications()
    {
        try {
            Notification::where('is_read', false)->update(['is_read' => true]);
            return $this->sendResponse([], 'All notifications read successfully');
        } catch (\Exception $e) {
            logError('HomeController', 'readAllNotifications', $e->getMessage());
            return $this->sendError('Error reading all notifications', [], 500);
        }
    }

    public function unreadNotificationsCount()
    {
        try {
            $count = Notification::unread()->count();
            return $this->sendResponse($count, 'Unread notifications count fetched successfully');
        } catch (\Exception $e) {
            logError('HomeController', 'unreadNotificationsCount', $e->getMessage());
            return $this->sendError('Error fetching unread notifications count', [], 500);
        }
    }

    public function getRecentActivities()
    {
        try {
            // Get recent works with their items
            $works = Work::with('workItems')
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($work) {
                    return [
                        'id' => $work->id,
                        'type' => 'work',
                        'from' => $work->from,
                        'title' => $work->name,
                        'description' => $work->description,
                        'amount' => $work->total,
                        'created_at' => $work->created_at,
                    ];
                });

            // Get recent payments
            $payments = Payment::latest()
                ->take(10)
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'type' => 'payment',
                        'title' => $payment->name,
                        'from' => $payment->name,
                        'description' => $payment->description,
                        'amount' => $payment->amount,
                        'created_at' => $payment->created_at,
                    ];
                });

            // Merge and sort activities by date
            $activities = $works->concat($payments)
                ->sortByDesc('created_at')
                ->take(10)
                ->values();

            return $this->sendResponse($activities, 'Recent activities fetched successfully');
        } catch (\Exception $e) {
            logError('HomeController', 'getRecentActivities', $e->getMessage());
            return $this->sendError('Error fetching recent activities', [], 500);
        }
    }
}

