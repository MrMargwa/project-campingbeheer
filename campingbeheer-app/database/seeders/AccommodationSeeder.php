<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        Accommodation::truncate();

        $imageCounters = [];

        foreach ($this->data() as $item) {
            $type = str_replace('Camping', 'Camper', $item['type']);
            $title = str_replace('Camping', 'Camper', $item['title']);

            $baseName = strtolower(str_replace(' ', '-', $type));

            if (!isset($imageCounters[$type])) {
                $files = glob(public_path('images') . '/' . $baseName . '-*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                $imageCounters[$type] = ['index' => 0, 'total' => count($files)];
            }

            $counter = &$imageCounters[$type];

            if ($counter['total'] > 0) {
                $imageIndex = ($counter['index'] % $counter['total']) + 1;
                $image = $baseName . '-' . $imageIndex . '.png';
                $counter['index']++;
            } else {
                $image = $baseName . '.png';
            }

            Accommodation::create([
                'title' => $title,
                'type' => $type,
                'description' => $item['description'],
                'min_persons' => $item['min_persons'],
                'max_persons' => $item['max_persons'],
                'price_per_night' => $item['price_per_night'],
                'image' => $image,
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
                'status' => 'available',
            ]);
        }
    }

    private function data(): array
    {
        return [
            ['title' => 'Camping 1', 'type' => 'Camping', 'description' => 'Ruime grasplek met wateraansluiting, geschikt voor tenten en caravans.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 27.50, 'latitude' => 53.096949, 'longitude' => 5.688182],
            ['title' => 'Camping 2', 'type' => 'Camping', 'description' => 'Zonnige plek met halfverharde ondergrond, dicht bij sanitair.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 22.50, 'latitude' => 53.097062, 'longitude' => 5.688268],
            ['title' => 'Camping 3', 'type' => 'Camping', 'description' => 'Ruime plek met elektra en picknicktafel, ideaal voor campers.', 'min_persons' => 2, 'max_persons' => 8, 'price_per_night' => 35.00, 'latitude' => 53.097145, 'longitude' => 5.688437],
            ['title' => 'Camping 4', 'type' => 'Camping', 'description' => 'Plek met uitzicht op het veld en eigen afvoer voor grijs water.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 30.00, 'latitude' => 53.096789, 'longitude' => 5.688348],
            ['title' => 'Camping 5', 'type' => 'Camping', 'description' => 'Stevige camperplek met water en elektra, rolstoelvriendelijk.', 'min_persons' => 1, 'max_persons' => 4, 'price_per_night' => 32.50, 'latitude' => 53.096928, 'longitude' => 5.688469],
            ['title' => 'Camping 6', 'type' => 'Camping', 'description' => 'Rustige plek omringd door struiken, ruimte voor grote tent.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 25.00, 'latitude' => 53.097012, 'longitude' => 5.688619],
            ['title' => 'Camping 7', 'type' => 'Camping', 'description' => 'Grote plek voor gezelschappen met vuurplaats en picknickbank.', 'min_persons' => 2, 'max_persons' => 10, 'price_per_night' => 42.50, 'latitude' => 53.096632, 'longitude' => 5.688536],
            ['title' => 'Camping 8', 'type' => 'Camping', 'description' => 'Beschutte plek met overdekte zithoek en eigen wastafel.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 27.50, 'latitude' => 53.096701, 'longitude' => 5.688724],
            ['title' => 'Camping 9', 'type' => 'Camping', 'description' => 'Vlakke zonnige plek dicht bij receptie en parkeerterrein.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 22.50, 'latitude' => 53.09683, 'longitude' => 5.688845],
            ['title' => 'Camping 10', 'type' => 'Camping', 'description' => 'Plek met eigen terras en verlichting, naast het centrale plein.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 30.00, 'latitude' => 53.096495, 'longitude' => 5.688724],
            ['title' => 'Camping 11', 'type' => 'Camping', 'description' => 'Natuurlijke schaduwboom en ruime oprit, prachtig uitzicht.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 27.50, 'latitude' => 53.096677, 'longitude' => 5.689059],

            ['title' => 'Vakantiehuis 1', 'type' => 'Vakantiehuis', 'description' => 'Sfeervol huis met authentieke uitstraling, houtkachel en eigen tuin.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 95.00, 'latitude' => 53.096599, 'longitude' => 5.687579],
            ['title' => 'Vakantiehuis 2', 'type' => 'Vakantiehuis', 'description' => 'Rustig gelegen met moderne inrichting, ruime woonkamer en volledige keuken.', 'min_persons' => 2, 'max_persons' => 8, 'price_per_night' => 110.00, 'latitude' => 53.096453, 'longitude' => 5.687723],
            ['title' => 'Vakantiehuis 3', 'type' => 'Vakantiehuis', 'description' => 'Zonnig huis met grote tuin en overdekt terras, ideaal voor gezinnen.', 'min_persons' => 3, 'max_persons' => 8, 'price_per_night' => 125.00, 'latitude' => 53.096554, 'longitude' => 5.687962],
            ['title' => 'Vakantiehuis 4', 'type' => 'Vakantiehuis', 'description' => 'Charmant met rieten dak, veranda met hangmat en twee verdiepingen.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 105.00, 'latitude' => 53.096699, 'longitude' => 5.687807],
            ['title' => 'Vakantiehuis 5', 'type' => 'Vakantiehuis', 'description' => 'Luxe huis met privésauna en openslaande deuren naar de tuin.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 150.00, 'latitude' => 53.096833, 'longitude' => 5.687812],
            ['title' => 'Vakantiehuis 6', 'type' => 'Vakantiehuis', 'description' => 'Gezellig met open haard en speelkamer, aan de rand van het park.', 'min_persons' => 2, 'max_persons' => 8, 'price_per_night' => 85.00, 'latitude' => 53.096727, 'longitude' => 5.68742],

            ['title' => 'Blokhut 1', 'type' => 'Blokhut', 'description' => 'Knusse houten blokhut met overkapping, eenvoudig maar sfeervol.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 55.00, 'latitude' => 53.096667, 'longitude' => 5.687045],
            ['title' => 'Blokhut 2', 'type' => 'Blokhut', 'description' => 'Blokhut met tuin, picknicktafel en vuurplaats voor een rustige vakantie.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 50.00, 'latitude' => 53.09671, 'longitude' => 5.687159],
            ['title' => 'Blokhut 3', 'type' => 'Blokhut', 'description' => 'Hut met beschut terras en overdekte berging voor fietsen.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 52.50, 'latitude' => 53.096744, 'longitude' => 5.687274],
            ['title' => 'Blokhut 4', 'type' => 'Blokhut', 'description' => 'Rustieke blokhut met houtkachel en eigen kookhoek in de natuur.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 57.50, 'latitude' => 53.096587, 'longitude' => 5.687112],
            ['title' => 'Blokhut 5', 'type' => 'Blokhut', 'description' => 'Lichte blokhut met veel ramen en veranda, ideaal voor ontbijt in de zon.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 60.00, 'latitude' => 53.096624, 'longitude' => 5.687233],
            ['title' => 'Blokhut 6', 'type' => 'Blokhut', 'description' => 'Omringd door bomen met schaduw, slaapzolder met extra bedden.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 67.50, 'latitude' => 53.096646, 'longitude' => 5.687345],
            ['title' => 'Blokhut 7', 'type' => 'Blokhut', 'description' => 'Ruime woonkamer, aparte eetkeuken en eigen sanitair voor een weekend.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 72.50, 'latitude' => 53.096515, 'longitude' => 5.687184],
            ['title' => 'Blokhut 8', 'type' => 'Blokhut', 'description' => 'Compacte blokhut voor stellen, rustige plek met uitzicht op het groen.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 47.50, 'latitude' => 53.096536, 'longitude' => 5.687292],
            ['title' => 'Blokhut 9', 'type' => 'Blokhut', 'description' => 'Sfeervol met veranda en ligweide, voorzien van koelkast en kookplaat.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 62.50, 'latitude' => 53.096569, 'longitude' => 5.687418],
            ['title' => 'Blokhut 10', 'type' => 'Blokhut', 'description' => 'Speelse indeling met stapelbed, terras met uitzicht op het veld.', 'min_persons' => 2, 'max_persons' => 5, 'price_per_night' => 57.50, 'latitude' => 53.096432, 'longitude' => 5.687257],
            ['title' => 'Blokhut 11', 'type' => 'Blokhut', 'description' => 'Traditioneel met rieten dak, houten wanden en knusse houtkachel.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 65.00, 'latitude' => 53.096456, 'longitude' => 5.687369],
            ['title' => 'Blokhut 12', 'type' => 'Blokhut', 'description' => 'Ruime overkapping en aparte slaapkamer beneden, geschikt voor elk seizoen.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 77.50, 'latitude' => 53.096477, 'longitude' => 5.687513],
            ['title' => 'Blokhut 13', 'type' => 'Blokhut', 'description' => 'Zonnige blokhut met privéterras, ligstoelen en eigen parkeerplaats.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 55.00, 'latitude' => 53.096359, 'longitude' => 5.687318],
            ['title' => 'Blokhut 14', 'type' => 'Blokhut', 'description' => 'Modern met strakke keuken en luxe douche, hout met hedendaags comfort.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 70.00, 'latitude' => 53.096378, 'longitude' => 5.687442],
            ['title' => 'Blokhut 15', 'type' => 'Blokhut', 'description' => 'Rustig aan het einde van een pad met vuurplaats en bloementuin.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 52.50, 'latitude' => 53.096406, 'longitude' => 5.687595],
            ['title' => 'Blokhut 16', 'type' => 'Blokhut', 'description' => 'Overdekte zithoek en hangmat tussen de bomen, compleet ingericht.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 50.00, 'latitude' => 53.096281, 'longitude' => 5.687379],
            ['title' => 'Blokhut 17', 'type' => 'Blokhut', 'description' => 'Hoog plafond met vide voor extra slaapruimte, deuren naar zonnig terras.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 72.50, 'latitude' => 53.096312, 'longitude' => 5.687509],
            ['title' => 'Blokhut 18', 'type' => 'Blokhut', 'description' => 'Knus met gashaard en zachte zithoek, romantisch weekendje weg.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 65.00, 'latitude' => 53.096339, 'longitude' => 5.687643],

            ['title' => 'Chalet 1', 'type' => 'Chalet', 'description' => 'Modern chalet met luxe uitstraling, ruim terras met loungeset en open keuken.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 110.00, 'latitude' => 53.096951, 'longitude' => 5.687214],
            ['title' => 'Chalet 2', 'type' => 'Chalet', 'description' => 'Stijlvol met houtkachel en panoramisch uitzicht, boxspringbedden.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 120.00, 'latitude' => 53.097018, 'longitude' => 5.687455],
            ['title' => 'Chalet 3', 'type' => 'Chalet', 'description' => 'Ruim chalet met eigen sauna en balkon, heerlijk ontspannen.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 145.00, 'latitude' => 53.097105, 'longitude' => 5.687691],
            ['title' => 'Chalet 4', 'type' => 'Chalet', 'description' => 'Gezinsvriendelijk met aparte kinderkamer, omheinde tuin en inloopdouche.', 'min_persons' => 3, 'max_persons' => 8, 'price_per_night' => 135.00, 'latitude' => 53.096825, 'longitude' => 5.687359],
            ['title' => 'Chalet 5', 'type' => 'Chalet', 'description' => 'Sfeervol met rieten dak en veranda rondom, open haard voor avonden.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 115.00, 'latitude' => 53.096894, 'longitude' => 5.687592],
            ['title' => 'Chalet 6', 'type' => 'Chalet', 'description' => 'Luxe met eigen hottub op overdekt terras en hoogwaardige keuken.', 'min_persons' => 2, 'max_persons' => 8, 'price_per_night' => 165.00, 'latitude' => 53.096954, 'longitude' => 5.687844],

            ['title' => 'Safaritent 1', 'type' => 'Safaritent', 'description' => 'Ruime safaritent met overkapping en eigen terras voor een avontuurlijke vakantie.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 49.50, 'latitude' => 53.097229, 'longitude' => 5.686935],
            ['title' => 'Safaritent 2', 'type' => 'Safaritent', 'description' => 'Luxe tent met comfortabele bedden en volledig ingerichte kitchenette.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 55.00, 'latitude' => 53.097324, 'longitude' => 5.687251],
            ['title' => 'Safaritent 3', 'type' => 'Safaritent', 'description' => 'Uniek met aparte slaapcabines, onvergetelijke kampeerervaring.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 62.50, 'latitude' => 53.097395, 'longitude' => 5.687514],
            ['title' => 'Safaritent 4', 'type' => 'Safaritent', 'description' => 'Sfeervol met houten vloeren en ruime woonkamer, overkapping voor schaduw.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 52.50, 'latitude' => 53.097511, 'longitude' => 5.687766],
            ['title' => 'Safaritent 5', 'type' => 'Safaritent', 'description' => 'Knusse tent met prachtig uitzicht, alle gemakken voor een zorgeloze vakantie.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 45.00, 'latitude' => 53.097611, 'longitude' => 5.688002],
            ['title' => 'Safaritent 6', 'type' => 'Safaritent', 'description' => 'Grote tent met zit-slaapcombinatie en eigen kookhoek, extra comfort.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 57.50, 'latitude' => 53.097029, 'longitude' => 5.687112],
            ['title' => 'Safaritent 7', 'type' => 'Safaritent', 'description' => 'Robuust ontwerp met warme uitstraling, heerlijk ontspannen op het terras.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 49.50, 'latitude' => 53.097126, 'longitude' => 5.687375],
            ['title' => 'Safaritent 8', 'type' => 'Safaritent', 'description' => 'Avontuurlijk met speelse indeling, veel opbergruimte en privacy.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 42.50, 'latitude' => 53.097221, 'longitude' => 5.687748],
            ['title' => 'Safaritent 9', 'type' => 'Safaritent', 'description' => 'Stijlvol met modern interieur en overdekt buitendek, kamperen in stijl.', 'min_persons' => 2, 'max_persons' => 5, 'price_per_night' => 60.00, 'latitude' => 53.097331, 'longitude' => 5.687994],
            ['title' => 'Safaritent 10', 'type' => 'Safaritent', 'description' => 'Compacte tent voor stellen, intieme kampeerervaring met basisvoorzieningen.', 'min_persons' => 2, 'max_persons' => 2, 'price_per_night' => 35.00, 'latitude' => 53.097439, 'longitude' => 5.688185],
            ['title' => 'Safaritent 11', 'type' => 'Safaritent', 'description' => 'Familievriendelijk met extra brede bedden, eethoek en loungemeubilair.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 55.00, 'latitude' => 53.0974, 'longitude' => 5.6890],
            ['title' => 'Safaritent 12', 'type' => 'Safaritent', 'description' => 'Zonnige tent met grote ramen, dicht bij sanitaire voorzieningen.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 40.00, 'latitude' => 53.09705, 'longitude' => 5.687938],
            ['title' => 'Safaritent 13', 'type' => 'Safaritent', 'description' => 'Knus ingericht met zachte tinten en natuurlijke materialen, perfecte uitvalsbasis.', 'min_persons' => 2, 'max_persons' => 4, 'price_per_night' => 45.00, 'latitude' => 53.097171, 'longitude' => 5.688158],
            ['title' => 'Safaritent 14', 'type' => 'Safaritent', 'description' => 'Ruim met aparte speelzone en eigen vuurplaats voor gezellige avonden.', 'min_persons' => 2, 'max_persons' => 6, 'price_per_night' => 65.00, 'latitude' => 53.097295, 'longitude' => 5.688324],
        ];
    }
}