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
            'Chalet' => 6,
            'Blokhut' => 12,
            'Camperplaats' => 20,
            'Safaritent' => 8,
            'Stacaravan' => 10,
            'Lodge' => 4,
            'Tiny House' => 5,
            'Bungalow' => 7,
            'Trekkershut' => 15,
            'Vakantiewoning' => 3,
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