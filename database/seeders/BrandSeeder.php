<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Acura',
                'country' => 'Japan',
                'description' => 'The luxury vehicle division of Japanese automaker Honda.',
                'logo_path' => 'brands/acura.png',
            ],
            [
                'name' => 'Alfa Romeo',
                'country' => 'Italy',
                'description' => 'A historic Italian aesthetic and performance luxury car manufacturer.',
                'logo_path' => 'brands/alfa_romeo.png',
            ],
            [
                'name' => 'Aston Martin',
                'country' => 'United Kingdom',
                'description' => 'A British manufacturer of luxury sports cars and grand tourers.',
                'logo_path' => 'brands/aston_martin.png',
            ],
            [
                'name' => 'Audi',
                'country' => 'Germany',
                'description' => 'A German luxury car manufacturer known for technology and performance.',
                'logo_path' => 'brands/audi.png',
            ],
            [
                'name' => 'Bentley',
                'country' => 'United Kingdom',
                'description' => 'A British manufacturer and marketer of luxury cars and SUVs.',
                'logo_path' => 'brands/bentley.png',
            ],
            [
                'name' => 'BMW',
                'country' => 'Germany',
                'description' => 'A multinational manufacturer of luxury vehicles and motorcycles.',
                'logo_path' => 'brands/bmw.png',
            ],
            [
                'name' => 'Bugatti',
                'country' => 'France',
                'description' => 'A French luxury manufacturer of high-performance hypercars.',
                'logo_path' => 'brands/bugatti.png',
            ],
            [
                'name' => 'Buick',
                'country' => 'United States',
                'description' => 'A premium automobile brand of General Motors.',
                'logo_path' => 'brands/buick.png',
            ],
            [
                'name' => 'BYD',
                'country' => 'China',
                'description' => 'A major Chinese manufacturer of electric vehicles and batteries.',
                'logo_path' => 'brands/byd.png',
            ],
            [
                'name' => 'Cadillac',
                'country' => 'United States',
                'description' => 'A division of General Motors that designs and builds luxury vehicles.',
                'logo_path' => 'brands/cadillac.png',
            ],
            [
                'name' => 'Chevrolet',
                'country' => 'United States',
                'description' => 'An American automobile division of the American manufacturer General Motors.',
                'logo_path' => 'brands/chevrolet.png',
            ],
            [
                'name' => 'Chrysler',
                'country' => 'United States',
                'description' => 'One of the "Big Three" automobile manufacturers in the United States.',
                'logo_path' => 'brands/chrysler.png',
            ],
            [
                'name' => 'Citroën',
                'country' => 'France',
                'description' => 'A French automobile brand owned by Stellantis, known for innovative technology.',
                'logo_path' => 'brands/citroen.png',
            ],
            [
                'name' => 'Dacia',
                'country' => 'Romania',
                'description' => 'A Romanian car manufacturer, an subsidiary of Renault.',
                'logo_path' => 'brands/dacia.png',
            ],
            [
                'name' => 'Dodge',
                'country' => 'United States',
                'description' => 'An American brand of automobiles, minivans, and SUVs.',
                'logo_path' => 'brands/dodge.png',
            ],
            [
                'name' => 'Ferrari',
                'country' => 'Italy',
                'description' => 'An Italian luxury sports car manufacturer based in Maranello.',
                'logo_path' => 'brands/ferrari.png',
            ],
            [
                'name' => 'Fiat',
                'country' => 'Italy',
                'description' => 'The largest automobile manufacturer in Italy.',
                'logo_path' => 'brands/fiat.png',
            ],
            [
                'name' => 'Ford',
                'country' => 'United States',
                'description' => 'An American multinational automobile manufacturer.',
                'logo_path' => 'brands/ford.png',
            ],
            [
                'name' => 'Genesis',
                'country' => 'South Korea',
                'description' => 'The luxury vehicle division of the South Korean vehicle manufacturer Hyundai Motor Group.',
                'logo_path' => 'brands/genesis.png',
            ],
            [
                'name' => 'GMC',
                'country' => 'United States',
                'description' => 'A division of General Motors that primarily focuses on trucks and utility vehicles.',
                'logo_path' => 'brands/gmc.png',
            ],
            [
                'name' => 'Honda',
                'country' => 'Japan',
                'description' => 'A Japanese public multinational conglomerate manufacturer of automobiles and power equipment.',
                'logo_path' => 'brands/honda.png',
            ],
            [
                'name' => 'Hyundai',
                'country' => 'South Korea',
                'description' => 'A South Korean multinational automotive manufacturer.',
                'logo_path' => 'brands/hyundai.png',
            ],
            [
                'name' => 'Infiniti',
                'country' => 'Japan',
                'description' => 'The luxury vehicle division of the Japanese automaker Nissan.',
                'logo_path' => 'brands/infiniti.png',
            ],
            [
                'name' => 'Jaguar',
                'country' => 'United Kingdom',
                'description' => 'The luxury vehicle brand of Jaguar Land Rover.',
                'logo_path' => 'brands/jaguar.png',
            ],
            [
                'name' => 'Jeep',
                'country' => 'United States',
                'description' => 'An American brand of automobiles principally consisting of luxury sport utility vehicles and off-road vehicles.',
                'logo_path' => 'brands/jeep.png',
            ],
            [
                'name' => 'Kia',
                'country' => 'South Korea',
                'description' => 'A South Korean multinational automobile manufacturer.',
                'logo_path' => 'brands/kia.png',
            ],
            [
                'name' => 'Koenigsegg',
                'country' => 'Sweden',
                'description' => 'A Swedish manufacturer of high-performance sports cars.',
                'logo_path' => 'brands/koenigsegg.png',
            ],
            [
                'name' => 'Lamborghini',
                'country' => 'Italy',
                'description' => 'An Italian brand and manufacturer of luxury supercars and SUVs.',
                'logo_path' => 'brands/lamborghini.png',
            ],
            [
                'name' => 'Land Rover',
                'country' => 'United Kingdom',
                'description' => 'A British brand of predominantly four-wheel drive, off-road capable vehicles.',
                'logo_path' => 'brands/land_rover.png',
            ],
            [
                'name' => 'Lexus',
                'country' => 'Japan',
                'description' => 'The luxury vehicle division of the Japanese automaker Toyota.',
                'logo_path' => 'brands/lexus.png',
            ],
            [
                'name' => 'Lincoln',
                'country' => 'United States',
                'description' => 'The luxury vehicle division of the American automobile manufacturer Ford.',
                'logo_path' => 'brands/lincoln.png',
            ],
            [
                'name' => 'Lotus',
                'country' => 'United Kingdom',
                'description' => 'A British automotive company known for sports and racing cars.',
                'logo_path' => 'brands/lotus.png',
            ],
            [
                'name' => 'Maserati',
                'country' => 'Italy',
                'description' => 'An Italian luxury vehicle manufacturer.',
                'logo_path' => 'brands/maserati.png',
            ],
            [
                'name' => 'Mazda',
                'country' => 'Japan',
                'description' => 'A Japanese multinational automaker.',
                'logo_path' => 'brands/mazda.png',
            ],
            [
                'name' => 'McLaren',
                'country' => 'United Kingdom',
                'description' => 'A British automotive manufacturer based at the McLaren Technology Centre.',
                'logo_path' => 'brands/mclaren.png',
            ],
            [
                'name' => 'Mercedes-Benz',
                'country' => 'Germany',
                'description' => 'A German global automobile marque and a division of Daimler AG.',
                'logo_path' => 'brands/mercedes_benz.png',
            ],
            [
                'name' => 'Mini',
                'country' => 'United Kingdom',
                'description' => 'A British automotive marque founded in 1969, owned by German automotive company BMW.',
                'logo_path' => 'brands/mini.png',
            ],
            [
                'name' => 'Mitsubishi',
                'country' => 'Japan',
                'description' => 'A Japanese multinational automotive manufacturer.',
                'logo_path' => 'brands/mitsubishi.png',
            ],
            [
                'name' => 'Nissan',
                'country' => 'Japan',
                'description' => 'A Japanese multinational automobile manufacturer.',
                'logo_path' => 'brands/nissan.png',
            ],
            [
                'name' => 'Opel',
                'country' => 'Germany',
                'description' => 'A German automobile manufacturer, a subsidiary of Stellantis.',
                'logo_path' => 'brands/opel.png',
            ],
            [
                'name' => 'Pagani',
                'country' => 'Italy',
                'description' => 'An Italian manufacturer of sports cars and carbon fibre components.',
                'logo_path' => 'brands/pagani.png',
            ],
            [
                'name' => 'Peugeot',
                'country' => 'France',
                'description' => 'A French brand of automobiles owned by Stellantis.',
                'logo_path' => 'brands/peugeot.png',
            ],
            [
                'name' => 'Porsche',
                'country' => 'Germany',
                'description' => 'A German automobile manufacturer specializing in high-performance sports cars.',
                'logo_path' => 'brands/porsche.png',
            ],
            [
                'name' => 'Ram',
                'country' => 'United States',
                'description' => 'An American brand of light to mid-weight commercial vehicles.',
                'logo_path' => 'brands/ram.png',
            ],
            [
                'name' => 'Renault',
                'country' => 'France',
                'description' => 'A French multinational automobile manufacturer.',
                'logo_path' => 'brands/renault.png',
            ],
            [
                'name' => 'Rolls-Royce',
                'country' => 'United Kingdom',
                'description' => 'A British luxury automobile maker.',
                'logo_path' => 'brands/rolls_royce.png',
            ],
            [
                'name' => 'Seat',
                'country' => 'Spain',
                'description' => 'A Spanish automobile manufacturer.',
                'logo_path' => 'brands/seat.png',
            ],
            [
                'name' => 'Skoda',
                'country' => 'Czech Republic',
                'description' => 'A Czech automobile manufacturer.',
                'logo_path' => 'brands/skoda.png',
            ],
            [
                'name' => 'Smart',
                'country' => 'Germany',
                'description' => 'A German automotive marque.',
                'logo_path' => 'brands/smart.png',
            ],
            [
                'name' => 'Subaru',
                'country' => 'Japan',
                'description' => 'A Japanese automobile manufacturing division of Subaru Corporation.',
                'logo_path' => 'brands/subaru.png',
            ],
            [
                'name' => 'Suzuki',
                'country' => 'Japan',
                'description' => 'A Japanese multinational corporation.',
                'logo_path' => 'brands/suzuki.png',
            ],
            [
                'name' => 'Tesla',
                'country' => 'United States',
                'description' => 'An American electric vehicle and clean energy company.',
                'logo_path' => 'brands/tesla.png',
            ],
            [
                'name' => 'Toyota',
                'country' => 'Japan',
                'description' => 'One of the largest automobile manufacturers in the world.',
                'logo_path' => 'brands/toyota.png',
            ],
            [
                'name' => 'Volkswagen',
                'country' => 'Germany',
                'description' => 'A German motor vehicle manufacturer.',
                'logo_path' => 'brands/volkswagen.png',
            ],
            [
                'name' => 'Volvo',
                'country' => 'Sweden',
                'description' => 'A Swedish multinational manufacturing company.',
                'logo_path' => 'brands/volvo.png',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['name' => $brand['name']],
                [
                    'country' => $brand['country'],
                    'description' => $brand['description'],
                    'logo_path' => $brand['logo_path'],
                ]
            );
        }
    }
}
