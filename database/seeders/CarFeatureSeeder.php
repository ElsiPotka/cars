<?php

namespace Database\Seeders;

use App\Models\CarFeature;
use Illuminate\Database\Seeder;

class CarFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            'ABS (Anti-lock Braking System)',
            'Airbags (Front, Side, Curtain)',
            'Traction Control System (TCS)',
            'Electronic Stability Program (ESP)',
            'Blind Spot Monitoring',
            'Lane Departure Warning',
            'Lane Keep Assist',
            'Forward Collision Warning',
            'Automatic Emergency Braking',
            'Adaptive Cruise Control',
            'Rearware Cross Traffic Alert',
            'Parking Sensors (Front & Rear)',
            '360-Degree Camera',
            'Backup Camera',
            'Security Alarm',
            'Immobilizer',
            'Isofix Child Seat Anchors',
            'Tire Pressure Monitoring System (TPMS)',
            'Air Conditioning',
            'Climate Control (Dual Zone)',
            'Keyless Entry',
            'Push Button Start',
            'Remote Start',
            'Power Windows',
            'Power Mirrors',
            'Power Locks',
            'Power Adjustable Seats',
            'Heated Seats (Front)',
            'Heated Seats (Rear)',
            'Ventilated Seats',
            'Heated Steering Wheel',
            'Leather Seats',
            'Sunroof / Moonroof',
            'Panoramic Roof',
            'Ambient Lighting',
            'Hands-Free Liftgate',
            'Rain-Sensing Wipers',
            'Auto-Dimming Rearview Mirror',
            'Bluetooth Connectivity',
            'Apple CarPlay',
            'Android Auto',
            'Touchscreen Display',
            'Navigation System',
            'Premium Sound System',
            'USB Ports',
            'Wireless Charging Pad',
            'Voice Control',
            'Head-Up Display (HUD)',
            'Digital Instrument Cluster',
            'Wi-Fi Hotspot',
            'Alloy Wheels',
            'Fog Lights',
            'LED Headlights',
            'LED Daytime Running Lights',
            'Roof Rails',
            'Tow Hitch',
            'Rear Spoiler',
            'Tinted Windows',
            'Sport Suspension',
            'Start-Stop System',
            'Paddle Shifters',
            'All-Wheel Drive (AWD)',
            '4-Wheel Drive (4WD)',
            'Turbocharged Engine',
        ];

        foreach ($features as $feature) {
            CarFeature::firstOrCreate(['name' => $feature]);
        }
    }
}
