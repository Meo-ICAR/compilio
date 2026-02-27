<?php

namespace Database\Seeders;

use App\Models\PracticeCommissionStatus;
use Illuminate\Database\Seeder;

class PracticeCommissionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'id' => 1,
                'status_payment' => null,
                'code' => null,
                'is_perfectioned' => false,
                'is_working' => true,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'status_payment' => 'Pratica perfezionata',
                'code' => null,
                'is_perfectioned' => false,
                'is_working' => true,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 3,
                'status_payment' => 'Pratica in lavorazione',
                'code' => null,
                'is_perfectioned' => false,
                'is_working' => true,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 4,
                'status_payment' => '',
                'code' => null,
                'is_perfectioned' => false,
                'is_working' => true,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 5,
                'status_payment' => 'Pratica deliberata',
                'code' => null,
                'is_perfectioned' => true,
                'is_working' => false,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 6,
                'status_payment' => 'Pratica erogata',
                'code' => null,
                'is_perfectioned' => false,
                'is_working' => true,
                'created_at' => null,
                'updated_at' => null,
            ],
        ];

        foreach ($statuses as $status) {
            PracticeCommissionStatus::create($status);
        }
    }
}
