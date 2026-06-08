<?php

namespace Database\Seeders;

use App\Models\Accommodatie;
use Illuminate\Database\Seeder;

class AccommodatieSeeder extends Seeder
{
    public function run(): void
    {
        $geojsonPath = base_path('/data.geojson');

        if (!file_exists($geojsonPath)) {
            echo "data.geojson niet gevonden. Sla AccommodatieSeeder over.\n";
            return;
        }

        $geojson = json_decode(file_get_contents($geojsonPath), true);
        $features = $geojson['features'] ?? [];

        foreach ($features as $feature) {
            $name = $feature['properties']['name'];
            $coords = $feature['geometry']['coordinates']; // [lng, lat]

            $type = preg_replace('/\s+\d+$/', '', $name);

            Accommodatie::create([
                'titel' => $name,
                'type' => $type,
                'beschrijving' => "Mooie {$type} geschikt voor vakantiegangers.",
                'min_personen' => rand(1, 4),
                'max_personen' => rand(4, 10),
                'prijs_per_nacht' => rand(50, 250) + 0.50,
                'afbeelding' => strtolower(str_replace(' ', '-', $type)) . '.jpg',
                'latitude' => $coords[1],
                'longitude' => $coords[0],
                'status' => 'beschikbaar',
            ]);
        }
    }
}