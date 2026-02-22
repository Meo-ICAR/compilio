<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FilamentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea utente Filament admin
        User::updateOrCreate(
            ['email' => 'hassistosrl@gmail.com'],
            [
                'name' => 'Hassisto Admin',
                'email' => 'hassistosrl@gmail.com',
                'password' => Hash::make('hassisto'),
                'company_id' => null, // NULL per Super Admin globali
            ]
        );

        $this->command->info('Utente Filament creato: hassistosrl@gmail.com / hassisto');
    }
}
