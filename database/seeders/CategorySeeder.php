<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'SUV',
                'description' => 'Sport Utility Vehicle, combines elements of road-going passenger cars with features from off-road vehicles.',
            ],
            [
                'name' => 'Sedan',
                'description' => 'A passenger car in a three-box configuration with separate compartments for engine, passenger, and cargo.',
            ],
            [
                'name' => 'Hatchback',
                'description' => 'A car body configuration with a rear door that swings upward to provide access to a cargo area.',
            ],
            [
                'name' => 'Coupe',
                'description' => 'A passenger car with a sloping or truncated rear roofline and generally two doors.',
            ],
            [
                'name' => 'Convertible',
                'description' => 'A passenger car that can be driven with or without a roof in place.',
            ],
            [
                'name' => 'Wagon',
                'description' => 'A car body style similar to a hatchback but with an extended rear cargo area.',
            ],
            [
                'name' => 'Pickup Truck',
                'description' => 'A light-duty truck having an enclosed cab and an open cargo area with low sides and tailgate.',
            ],
            [
                'name' => 'Minivan',
                'description' => 'A vehicle designed primarily for passenger safety and comfort, often with sliding rear doors.',
            ],
            [
                'name' => 'Electric',
                'description' => 'Vehicles powered entirely by electricity.',
            ],
            [
                'name' => 'Hybrid',
                'description' => 'Vehicles powered by both gasoline and electric motors.',
            ],
            [
                'name' => 'Luxury',
                'description' => 'High-end vehicles offering superior comfort, features, and performance.',
            ],
            [
                'name' => 'Sports Car',
                'description' => 'A car designed with an emphasis on dynamic performance, such as handling, acceleration, top speed, or thrill of driving.',
            ],
            [
                'name' => 'Crossover',
                'description' => 'A vehicle that combines features of an SUV with those of a passenger car, typically built on a car platform.',
            ],
            [
                'name' => 'Diesel',
                'description' => 'Vehicles powered by a diesel engine.',
            ],
            [
                'name' => 'Off-Road',
                'description' => 'Vehicles designed specifically for driving on unpaved surfaces.',
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
