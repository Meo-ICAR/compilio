<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FirrSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $firrs = [
            [
                'id' => 1,
                'minimo' => 0.00,
                'massimo' => 6200.00,
                'aliquota' => 4.00,
                'competenza' => 2025,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'minimo' => 6201.00,
                'massimo' => 9300.00,
                'aliquota' => 2.00,
                'competenza' => 2025,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // ... altri record
        ];

        foreach ($firrs as $firr) {
            \App\Models\Firr::updateOrCreate(['id' => $firr['id']], $firr);
        }
    }
            [
                'id' => 1,
                'minimo' => 0.00,
                'massimo' => 6200.00,
                'aliquota' => 4.00,
                'competenza' => 2025,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'minimo' => 6201.00,
                'massimo' => 9300.00,
                'aliquota' => 2.00,
                'competenza' => 2025,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'minimo' => 9301.00,
                'massimo' => 99999999.00,
                'aliquota' => 1.00,
                'competenza' => 2025,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'minimo' => 0.00,
                'massimo' => 12400.00,
                'aliquota' => 4.00,
                'competenza' => 2025,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'minimo' => 12401.00,
                'massimo' => 18600.00,
                'aliquota' => 2.00,
                'competenza' => 2025,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'minimo' => 18601.00,
                'massimo' => 99999999.00,
                'aliquota' => 1.00,
                'competenza' => 2025,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'minimo' => 0.00,
                'massimo' => 6200.00,
                'aliquota' => 4.00,
                'competenza' => 2026,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'minimo' => 6201.00,
                'massimo' => 9300.00,
                'aliquota' => 2.00,
                'competenza' => 2026,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'minimo' => 9301.00,
                'massimo' => 99999999.00,
                'aliquota' => 1.00,
                'competenza' => 2026,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'minimo' => 0.00,
                'massimo' => 12400.00,
                'aliquota' => 4.00,
                'competenza' => 2026,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'minimo' => 12401.00,
                'massimo' => 18600.00,
                'aliquota' => 2.00,
                'competenza' => 2026,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'minimo' => 18601.00,
                'massimo' => 99999999.00,
                'aliquota' => 1.00,
                'competenza' => 2026,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
