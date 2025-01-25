<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            [
                'title' => 'Welcome to HiraBook',
                'description' => 'Thank you for joining HiraBook. Start managing your work and payments efficiently.',
                'image' => 'welcome.png',
                'link' => '/dashboard',
                'link_text' => 'Go to Dashboard',
                'link_icon' => 'dashboard',
                'link_color' => '#4CAF50',
            ],
            [
                'title' => 'New Payment Feature',
                'description' => 'We\'ve added new payment tracking features to help you manage your finances better.',
                'image' => 'payment.png',
                'link' => '/payments',
                'link_text' => 'Try Now',
                'link_icon' => 'payment',
                'link_color' => '#2196F3',
            ],
        ];

        // Common notification types for generating random data
        $notificationTypes = [
            [
                'title' => 'New Work Added',
                'description' => 'A new work item has been added to your dashboard.',
                'link' => '/works',
                'link_text' => 'View Work',
                'link_icon' => 'work',
                'link_color' => '#FF9800',
            ],
            [
                'title' => 'Payment Received',
                'description' => 'You have received a new payment.',
                'link' => '/payments',
                'link_text' => 'View Payment',
                'link_icon' => 'payment',
                'link_color' => '#4CAF50',
            ],
            [
                'title' => 'System Update',
                'description' => 'We\'ve updated our system with new features.',
                'link' => '/updates',
                'link_text' => 'Learn More',
                'link_icon' => 'update',
                'link_color' => '#9C27B0',
            ],
            [
                'title' => 'Security Alert',
                'description' => 'Important security update for your account.',
                'link' => '/security',
                'link_text' => 'Review',
                'link_icon' => 'security',
                'link_color' => '#F44336',
            ],
        ];

        // Insert the fixed notifications first
        foreach ($notifications as $notification) {
            Notification::create(array_merge($notification, [
                'is_read' => false,
                'status' => true,
            ]));
        }

        // Generate 28 more random notifications to make total 30
        for ($i = 0; $i < 28; $i++) {
            $type = $notificationTypes[array_rand($notificationTypes)];
            $randomDays = rand(1, 30);

            Notification::create([
                'title' => $type['title'] . ' #' . ($i + 1),
                'description' => $type['description'],
                'image' => null,
                'link' => $type['link'],
                'link_text' => $type['link_text'],
                'link_icon' => $type['link_icon'],
                'link_color' => $type['link_color'],
                'is_read' => (bool) rand(0, 1),
                'status' => true,
                'created_at' => now()->subDays($randomDays),
                'updated_at' => now()->subDays($randomDays),
            ]);
        }
    }
}
