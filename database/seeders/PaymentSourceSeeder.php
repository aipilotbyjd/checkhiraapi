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
            ['name' => 'Cash'],
            ['name' => 'Bank Transfer'],
            ['name' => 'UPI'],
            ['name' => 'Cheque'],
            ['name' => 'Card'],
            ['name' => 'Other'],
        ];

        foreach ($paymentSources as $paymentSource) {
            PaymentSource::create($paymentSource);
        }
    }
}
