<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "naam" => "Beheerder 01",
            "email" => "beheerder01@campingbeheer.nl",
            "wachtwoord" => bcrypt("password123"),
            "rol" => "admin"
        ]);
        User::create([
            "naam" => "Frans de Boer",
            "email" => "frans@campingbeheer.nl",
            "wachtwoord" => bcrypt("password123"),
            "rol" => "admin"
        ]);
    }
}