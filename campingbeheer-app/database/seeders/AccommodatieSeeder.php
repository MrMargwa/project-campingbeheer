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

        Accommodatie::truncate();

        $geojson = json_decode(file_get_contents($geojsonPath), true);
        $features = $geojson['features'] ?? [];

        $imageCounters = [];

        foreach ($features as $feature) {
            if ($feature['geometry']['type'] !== 'Point') {
                continue;
            }

            $name = $feature['properties']['name'];
            $coords = $feature['geometry']['coordinates'];

            $type = preg_replace('/\s+\d+$/', '', $name);
            $type = str_replace('Camping', 'Camper', $type);
            $name = str_replace('Camping', 'Camper', $name);

            $baseName = strtolower(str_replace(' ', '-', $type));

            if (!isset($imageCounters[$type])) {
                $imageFiles = glob(public_path('images') . '/' . $baseName . '-*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                $imageCounters[$type] = ['index' => 0, 'total' => count($imageFiles)];
            }

            $counter = &$imageCounters[$type];

            if ($counter['total'] > 0) {
                $imageIndex = ($counter['index'] % $counter['total']) + 1;
                $afbeelding = $baseName . '-' . $imageIndex . '.png';
                $counter['index']++;
            } else {
                $afbeelding = $baseName . '.png';
            }

            Accommodatie::create([
                'titel' => $name,
                'type' => $type,
                'beschrijving' => "Mooie {$type} geschikt voor vakantiegangers.",
                'min_personen' => rand(1, 4),
                'max_personen' => rand(4, 10),
                'prijs_per_nacht' => rand(50, 250) + 0.50,
                'afbeelding' => $afbeelding,
                'latitude' => $coords[1],
                'longitude' => $coords[0],
                'status' => 'beschikbaar',
            ]);
        }
    }
}