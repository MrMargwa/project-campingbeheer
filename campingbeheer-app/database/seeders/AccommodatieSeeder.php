<?php

namespace Database\Seeders;

use App\Models\Accommodatie;
use Illuminate\Database\Seeder;

class AccommodatieSeeder extends Seeder
{
    public function run(): void
    {
        Accommodatie::truncate();

        $imageCounters = [];

        foreach ($this->data() as $item) {
            $type = str_replace('Camping', 'Camper', $item['type']);
            $titel = str_replace('Camping', 'Camper', $item['titel']);

            $baseName = strtolower(str_replace(' ', '-', $type));

            if (!isset($imageCounters[$type])) {
                $files = glob(public_path('images') . '/' . $baseName . '-*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                $imageCounters[$type] = ['index' => 0, 'total' => count($files)];
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
                'titel' => $titel,
                'type' => $type,
                'beschrijving' => $item['beschrijving'],
                'min_personen' => $item['min_personen'],
                'max_personen' => $item['max_personen'],
                'huisdieren_toegestaan' => $item['huisdieren_toegestaan'],
                'prijs_per_nacht' => $item['prijs_per_nacht'],
                'afbeelding' => $afbeelding,
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
                'status' => 'beschikbaar',
            ]);
        }
    }

    private function data(): array
    {
        return [
            ['titel' => 'Camping 1', 'type' => 'Camping', 'beschrijving' => 'Ruime grasplek met wateraansluiting, geschikt voor tenten en caravans.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 27.50, 'latitude' => 53.0971, 'longitude' => 5.6868],
            ['titel' => 'Camping 2', 'type' => 'Camping', 'beschrijving' => 'Zonnige plek met halfverharde ondergrond, dicht bij sanitair.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 22.50, 'latitude' => 53.0973, 'longitude' => 5.6872],
            ['titel' => 'Camping 3', 'type' => 'Camping', 'beschrijving' => 'Ruime plek met elektra en picknicktafel, ideaal voor campers.', 'min_personen' => 2, 'max_personen' => 8, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 35.00, 'latitude' => 53.0968, 'longitude' => 5.6865],
            ['titel' => 'Camping 4', 'type' => 'Camping', 'beschrijving' => 'Plek met uitzicht op het veld en eigen afvoer voor grijs water.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 30.00, 'latitude' => 53.0970, 'longitude' => 5.6878],
            ['titel' => 'Camping 5', 'type' => 'Camping', 'beschrijving' => 'Stevige camperplek met water en elektra, rolstoelvriendelijk.', 'min_personen' => 1, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 32.50, 'latitude' => 53.0974, 'longitude' => 5.6862],
            ['titel' => 'Camping 6', 'type' => 'Camping', 'beschrijving' => 'Rustige plek omringd door struiken, ruimte voor grote tent.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 25.00, 'latitude' => 53.0966, 'longitude' => 5.6875],
            ['titel' => 'Camping 7', 'type' => 'Camping', 'beschrijving' => 'Grote plek voor gezelschappen met vuurplaats en picknickbank.', 'min_personen' => 2, 'max_personen' => 10, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 42.50, 'latitude' => 53.0972, 'longitude' => 5.6860],
            ['titel' => 'Camping 8', 'type' => 'Camping', 'beschrijving' => 'Beschutte plek met overdekte zithoek en eigen wastafel.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 27.50, 'latitude' => 53.0969, 'longitude' => 5.6882],
            ['titel' => 'Camping 9', 'type' => 'Camping', 'beschrijving' => 'Vlakke zonnige plek dicht bij receptie en parkeerterrein.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 22.50, 'latitude' => 53.0975, 'longitude' => 5.6870],
            ['titel' => 'Camping 10', 'type' => 'Camping', 'beschrijving' => 'Plek met eigen terras en verlichting, naast het centrale plein.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 30.00, 'latitude' => 53.0967, 'longitude' => 5.6866],
            ['titel' => 'Camping 11', 'type' => 'Camping', 'beschrijving' => 'Natuurlijke schaduwboom en ruime oprit, prachtig uitzicht.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 27.50, 'latitude' => 53.0971, 'longitude' => 5.6880],

            ['titel' => 'Vakantiehuis 1', 'type' => 'Vakantiehuis', 'beschrijving' => 'Sfeervol huis met authentieke uitstraling, houtkachel en eigen tuin.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 95.00, 'latitude' => 53.0964, 'longitude' => 5.6886],
            ['titel' => 'Vakantiehuis 2', 'type' => 'Vakantiehuis', 'beschrijving' => 'Rustig gelegen met moderne inrichting, ruime woonkamer en volledige keuken.', 'min_personen' => 2, 'max_personen' => 8, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 110.00, 'latitude' => 53.0966, 'longitude' => 5.6888],
            ['titel' => 'Vakantiehuis 3', 'type' => 'Vakantiehuis', 'beschrijving' => 'Zonnig huis met grote tuin en overdekt terras, ideaal voor gezinnen.', 'min_personen' => 3, 'max_personen' => 8, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 125.00, 'latitude' => 53.0962, 'longitude' => 5.6883],
            ['titel' => 'Vakantiehuis 4', 'type' => 'Vakantiehuis', 'beschrijving' => 'Charmant met rieten dak, veranda met hangmat en twee verdiepingen.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 105.00, 'latitude' => 53.0967, 'longitude' => 5.6880],
            ['titel' => 'Vakantiehuis 5', 'type' => 'Vakantiehuis', 'beschrijving' => 'Luxe huis met privésauna en openslaande deuren naar de tuin.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 150.00, 'latitude' => 53.0963, 'longitude' => 5.6890],
            ['titel' => 'Vakantiehuis 6', 'type' => 'Vakantiehuis', 'beschrijving' => 'Gezellig met open haard en speelkamer, aan de rand van het park.', 'min_personen' => 2, 'max_personen' => 8, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 85.00, 'latitude' => 53.0968, 'longitude' => 5.6885],

            ['titel' => 'Blokhut 1', 'type' => 'Blokhut', 'beschrijving' => 'Knusse houten blokhut met overkapping, eenvoudig maar sfeervol.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 55.00, 'latitude' => 53.0976, 'longitude' => 5.6874],
            ['titel' => 'Blokhut 2', 'type' => 'Blokhut', 'beschrijving' => 'Blokhut met tuin, picknicktafel en vuurplaats voor een rustige vakantie.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 50.00, 'latitude' => 53.0978, 'longitude' => 5.6878],
            ['titel' => 'Blokhut 3', 'type' => 'Blokhut', 'beschrijving' => 'Hut met beschut terras en overdekte berging voor fietsen.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 52.50, 'latitude' => 53.0974, 'longitude' => 5.6872],
            ['titel' => 'Blokhut 4', 'type' => 'Blokhut', 'beschrijving' => 'Rustieke blokhut met houtkachel en eigen kookhoek in de natuur.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 57.50, 'latitude' => 53.0980, 'longitude' => 5.6876],
            ['titel' => 'Blokhut 5', 'type' => 'Blokhut', 'beschrijving' => 'Lichte blokhut met veel ramen en veranda, ideaal voor ontbijt in de zon.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 60.00, 'latitude' => 53.0973, 'longitude' => 5.6880],
            ['titel' => 'Blokhut 6', 'type' => 'Blokhut', 'beschrijving' => 'Omringd door bomen met schaduw, slaapzolder met extra bedden.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 67.50, 'latitude' => 53.0977, 'longitude' => 5.6870],
            ['titel' => 'Blokhut 7', 'type' => 'Blokhut', 'beschrijving' => 'Ruime woonkamer, aparte eetkeuken en eigen sanitair voor een weekend.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 72.50, 'latitude' => 53.0981, 'longitude' => 5.6875],
            ['titel' => 'Blokhut 8', 'type' => 'Blokhut', 'beschrijving' => 'Compacte blokhut voor stellen, rustige plek met uitzicht op het groen.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 47.50, 'latitude' => 53.0975, 'longitude' => 5.6882],
            ['titel' => 'Blokhut 9', 'type' => 'Blokhut', 'beschrijving' => 'Sfeervol met veranda en ligweide, voorzien van koelkast en kookplaat.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 62.50, 'latitude' => 53.0972, 'longitude' => 5.6873],
            ['titel' => 'Blokhut 10', 'type' => 'Blokhut', 'beschrijving' => 'Speelse indeling met stapelbed, terras met uitzicht op het veld.', 'min_personen' => 2, 'max_personen' => 5, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 57.50, 'latitude' => 53.0979, 'longitude' => 5.6879],
            ['titel' => 'Blokhut 11', 'type' => 'Blokhut', 'beschrijving' => 'Traditioneel met rieten dak, houten wanden en knusse houtkachel.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 65.00, 'latitude' => 53.0974, 'longitude' => 5.6868],
            ['titel' => 'Blokhut 12', 'type' => 'Blokhut', 'beschrijving' => 'Ruime overkapping en aparte slaapkamer beneden, geschikt voor elk seizoen.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 77.50, 'latitude' => 53.0982, 'longitude' => 5.6872],
            ['titel' => 'Blokhut 13', 'type' => 'Blokhut', 'beschrijving' => 'Zonnige blokhut met privéterras, ligstoelen en eigen parkeerplaats.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 55.00, 'latitude' => 53.0971, 'longitude' => 5.6877],
            ['titel' => 'Blokhut 14', 'type' => 'Blokhut', 'beschrijving' => 'Modern met strakke keuken en luxe douche, hout met hedendaags comfort.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 70.00, 'latitude' => 53.0976, 'longitude' => 5.6865],
            ['titel' => 'Blokhut 15', 'type' => 'Blokhut', 'beschrijving' => 'Rustig aan het einde van een pad met vuurplaats en bloementuin.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 52.50, 'latitude' => 53.0983, 'longitude' => 5.6880],
            ['titel' => 'Blokhut 16', 'type' => 'Blokhut', 'beschrijving' => 'Overdekte zithoek en hangmat tussen de bomen, compleet ingericht.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 50.00, 'latitude' => 53.0970, 'longitude' => 5.6871],
            ['titel' => 'Blokhut 17', 'type' => 'Blokhut', 'beschrijving' => 'Hoog plafond met vide voor extra slaapruimte, deuren naar zonnig terras.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 72.50, 'latitude' => 53.0978, 'longitude' => 5.6868],
            ['titel' => 'Blokhut 18', 'type' => 'Blokhut', 'beschrijving' => 'Knus met gashaard en zachte zithoek, romantisch weekendje weg.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 65.00, 'latitude' => 53.0973, 'longitude' => 5.6884],

            ['titel' => 'Chalet 1', 'type' => 'Chalet', 'beschrijving' => 'Modern chalet met luxe uitstraling, ruim terras met loungeset en open keuken.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 110.00, 'latitude' => 53.0964, 'longitude' => 5.6864],
            ['titel' => 'Chalet 2', 'type' => 'Chalet', 'beschrijving' => 'Stijlvol met houtkachel en panoramisch uitzicht, boxspringbedden.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 120.00, 'latitude' => 53.0966, 'longitude' => 5.6868],
            ['titel' => 'Chalet 3', 'type' => 'Chalet', 'beschrijving' => 'Ruim chalet met eigen sauna en balkon, heerlijk ontspannen.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 145.00, 'latitude' => 53.0962, 'longitude' => 5.6862],
            ['titel' => 'Chalet 4', 'type' => 'Chalet', 'beschrijving' => 'Gezinsvriendelijk met aparte kinderkamer, omheinde tuin en inloopdouche.', 'min_personen' => 3, 'max_personen' => 8, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 135.00, 'latitude' => 53.0967, 'longitude' => 5.6866],
            ['titel' => 'Chalet 5', 'type' => 'Chalet', 'beschrijving' => 'Sfeervol met rieten dak en veranda rondom, open haard voor avonden.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 115.00, 'latitude' => 53.0963, 'longitude' => 5.6870],
            ['titel' => 'Chalet 6', 'type' => 'Chalet', 'beschrijving' => 'Luxe met eigen hottub op overdekt terras en hoogwaardige keuken.', 'min_personen' => 2, 'max_personen' => 8, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 165.00, 'latitude' => 53.0968, 'longitude' => 5.6860],

            ['titel' => 'Safaritent 1', 'type' => 'Safaritent', 'beschrijving' => 'Ruime safaritent met overkapping en eigen terras voor een avontuurlijke vakantie.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 49.50, 'latitude' => 53.0976, 'longitude' => 5.6886],
            ['titel' => 'Safaritent 2', 'type' => 'Safaritent', 'beschrijving' => 'Luxe tent met comfortabele bedden en volledig ingerichte kitchenette.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 55.00, 'latitude' => 53.0978, 'longitude' => 5.6890],
            ['titel' => 'Safaritent 3', 'type' => 'Safaritent', 'beschrijving' => 'Uniek met aparte slaapcabines, onvergetelijke kampeerervaring.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 62.50, 'latitude' => 53.0974, 'longitude' => 5.6884],
            ['titel' => 'Safaritent 4', 'type' => 'Safaritent', 'beschrijving' => 'Sfeervol met houten vloeren en ruime woonkamer, overkapping voor schaduw.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 52.50, 'latitude' => 53.0980, 'longitude' => 5.6888],
            ['titel' => 'Safaritent 5', 'type' => 'Safaritent', 'beschrijving' => 'Knusse tent met prachtig uitzicht, alle gemakken voor een zorgeloze vakantie.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 45.00, 'latitude' => 53.0973, 'longitude' => 5.6882],
            ['titel' => 'Safaritent 6', 'type' => 'Safaritent', 'beschrijving' => 'Grote tent met zit-slaapcombinatie en eigen kookhoek, extra comfort.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 57.50, 'latitude' => 53.0977, 'longitude' => 5.6892],
            ['titel' => 'Safaritent 7', 'type' => 'Safaritent', 'beschrijving' => 'Robuust ontwerp met warme uitstraling, heerlijk ontspannen op het terras.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 49.50, 'latitude' => 53.0981, 'longitude' => 5.6880],
            ['titel' => 'Safaritent 8', 'type' => 'Safaritent', 'beschrijving' => 'Avontuurlijk met speelse indeling, veel opbergruimte en privacy.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 42.50, 'latitude' => 53.0975, 'longitude' => 5.6885],
            ['titel' => 'Safaritent 9', 'type' => 'Safaritent', 'beschrijving' => 'Stijlvol met modern interieur en overdekt buitendek, kamperen in stijl.', 'min_personen' => 2, 'max_personen' => 5, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 60.00, 'latitude' => 53.0972, 'longitude' => 5.6889],
            ['titel' => 'Safaritent 10', 'type' => 'Safaritent', 'beschrijving' => 'Compacte tent voor stellen, intieme kampeerervaring met basisvoorzieningen.', 'min_personen' => 2, 'max_personen' => 2, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 35.00, 'latitude' => 53.0979, 'longitude' => 5.6883],
            ['titel' => 'Safaritent 11', 'type' => 'Safaritent', 'beschrijving' => 'Familievriendelijk met extra brede bedden, eethoek en loungemeubilair.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 55.00, 'latitude' => 53.0974, 'longitude' => 5.6890],
            ['titel' => 'Safaritent 12', 'type' => 'Safaritent', 'beschrijving' => 'Zonnige tent met grote ramen, dicht bij sanitaire voorzieningen.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 40.00, 'latitude' => 53.0982, 'longitude' => 5.6886],
            ['titel' => 'Safaritent 13', 'type' => 'Safaritent', 'beschrijving' => 'Knus ingericht met zachte tinten en natuurlijke materialen, perfecte uitvalsbasis.', 'min_personen' => 2, 'max_personen' => 4, 'huisdieren_toegestaan' => true, 'prijs_per_nacht' => 45.00, 'latitude' => 53.0971, 'longitude' => 5.6887],
            ['titel' => 'Safaritent 14', 'type' => 'Safaritent', 'beschrijving' => 'Ruim met aparte speelzone en eigen vuurplaats voor gezellige avonden.', 'min_personen' => 2, 'max_personen' => 6, 'huisdieren_toegestaan' => false, 'prijs_per_nacht' => 65.00, 'latitude' => 53.0978, 'longitude' => 5.6892],
        ];
    }
}
