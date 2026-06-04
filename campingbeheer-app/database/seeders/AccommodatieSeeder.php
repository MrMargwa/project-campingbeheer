<?php

namespace Database\Seeders;

use App\Models\Accommodatie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccommodatieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Chalet' => 8,
            'Blokhut' => 10,
            'Camperplaats' => 16,
            'Safaritent' => 9,
            'Vakantiewoning' => 7,
        ];

        foreach ($types as $type => $aantal) {

            for ($i = 1; $i <= $aantal; $i++) {

                Accommodatie::create([
                    'titel' => "{$type} {$i}",
                    'type' => $type,
                    'beschrijving' => "Mooie {$type} geschikt voor vakantiegangers.",
                    'min_personen' => rand(1, 4),
                    'max_personen' => rand(4, 10),
                    'prijs_per_nacht' => rand(50, 250),
                    'afbeelding' => strtolower(str_replace(' ', '-', $type)) . '.jpg',
                    'status' => 'beschikbaar',
                ]);
            }
        }
    }
}