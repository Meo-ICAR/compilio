<?php

namespace Database\Seeders;

use App\Models\Company;
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
        $companyId = Company::where('vat_number', '05822361007')->first()->id;
        // Crea utente Filament admin
        User::updateOrCreate(
            ['email' => 'hassistosrl@gmail.com'],
            [
                'name' => 'Hassisto Admin',
                'email' => 'hassistosrl@gmail.com',
                'password' => Hash::make('password'),
                'company_id' => $companyId,  // NULL per Super Admin globali
            ]
        );
        User::updateOrCreate(
            ['email' => 'mario@globadvisor.it'],
            [
                'name' => 'Mario Gargiulo',
                'email' => 'mario@globadvisor.it',
                'password' => Hash::make('password'),
                'company_id' => $companyId,  // NULL per Super Admin globali
            ]
        );
        User::updateOrCreate(
            ['email' => 'sergio@bracale.it'],
            [
                'name' => 'Sergio Bracale',
                'email' => 'sergio.bracale@races.it',
                'password' => Hash::make('password'),
                'company_id' => $companyId,  // NULL per Super Admin globali
            ]
        );
    }
}
