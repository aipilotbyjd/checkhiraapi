<?php

namespace Database\Seeders;

use App\Models\PaymentSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentSources = [
            ['name' => 'Cash', 'icon' => 'cash'],
            ['name' => 'Bank Transfer', 'icon' => 'bank-transfer-in'],
            ['name' => 'UPI', 'icon' => 'qrcode'],
            ['name' => 'Cheque', 'icon' => 'file-document-outline'],
            ['name' => 'Card', 'icon' => 'credit-card'],
            ['name' => 'Other', 'icon' => 'dots-horizontal'],
        ];

        foreach ($paymentSources as $paymentSource) {
            PaymentSource::create($paymentSource);
        }
    }
}
