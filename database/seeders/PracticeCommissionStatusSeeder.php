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
                'name' => 'pratica perfezionata',
                'code' => 'Mediafacile',
                'is_perfectioned' => 1,
                'is_working' => null,
            ],
            [
                'name' => 'pratica in lavorazione',
                'code' => 'Mediafacile',
                'is_perfectioned' => 0,
                'is_working' => 1,
            ],
            [
                'name' => 'pratica deliberata',
                'code' => 'Mediafacile',
                'is_perfectioned' => 0,
                'is_working' => 1,
            ],
            [
                'name' => 'pratica erogata',
                'code' => 'Mediafacile',
                'is_perfectioned' => 0,
                'is_working' => 1,
            ],
            [
                'name' => '',  // Sostituito la stringa vuota con un valore leggibile
                'code' => 'Mediafacile',
                'is_perfectioned' => null,
                'is_working' => null,
            ],
        ];

        foreach ($statuses as $status) {
            PracticeCommissionStatus::create($status);
        }
    }
}
