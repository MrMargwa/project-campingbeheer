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
            "name" => "Beheerder 01",
            "email" => "beheerder01@campingbeheer.nl",
            "password" => bcrypt("password123"),
            "role" => "admin"
        ]);
        User::create([
            "name" => "Frans de Boer",
            "email" => "frans@campingbeheer.nl",
            "password" => bcrypt("password123"),
            "role" => "admin"
        ]);
        User::create([
            "name" => "kijkinteams",
            "email" => "kijkinteams@gmail.com",
            "password" => bcrypt("jonge"),
            "role" => "admin"
        ]);
  
    }
}