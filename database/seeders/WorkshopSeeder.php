<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class WorkshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'manager@example.com')->first();
        Auth::setUser($manager);

        $records = [
            [
                'code' => 'POT-101',
                'title' => 'Wheel Throwing for Beginners',
                'description' => 'Centre a lump of clay and throw your first bowl. Aprons and tools provided.',
                'instructor' => 'Hana Kimura',
                'location' => 'Central',
                'starts_at' => today()->addDays(2)->setTime(10, 0),
                'ends_at' => today()->addDays(2)->setTime(13, 0),
                'capacity' => 12,
                'status' => 'scheduled',
            ],
            [
                'code' => 'CODE-201',
                'title' => 'Intro to Python',
                'description' => 'Variables, loops and functions for complete beginners. Bring a laptop.',
                'instructor' => 'Daniel Okafor',
                'location' => 'Northside',
                'starts_at' => today()->addDay()->setTime(18, 0),
                'ends_at' => today()->addDay()->setTime(20, 0),
                'capacity' => 20,
                'status' => 'scheduled',
            ],
            [
                'code' => 'FIT-110',
                'title' => 'Morning HIIT',
                'description' => 'A 60-minute high-intensity interval session for all fitness levels.',
                'instructor' => 'Priya Nair',
                'location' => 'Riverside',
                'starts_at' => today()->addDays(4)->setTime(9, 0),
                'ends_at' => today()->addDays(4)->setTime(10, 0),
                'capacity' => 15,
                'status' => 'scheduled',
            ],
            [
                'code' => 'ART-150',
                'title' => 'Watercolour Landscapes',
                'description' => 'Washes, layering and simple landscapes. All materials included.',
                'instructor' => 'Tom Whitfield',
                'location' => 'Central',
                'starts_at' => today()->addDays(8)->setTime(14, 0),
                'ends_at' => today()->addDays(8)->setTime(16, 0),
                'capacity' => 10,
                'status' => 'scheduled',
            ],
            [
                'code' => 'PHO-120',
                'title' => 'Smartphone Photography',
                'description' => 'Composition, light and editing using only the phone in your pocket.',
                'instructor' => 'Lucia Romero',
                'location' => 'Riverside',
                'starts_at' => today()->addDays(12)->setTime(11, 0),
                'ends_at' => today()->addDays(12)->setTime(13, 0),
                'capacity' => 16,
                'status' => 'scheduled',
            ],
        ];

        foreach ($records as $record) {
            Workshop::create([
                ...$record,
                'created_by' => $manager->id,
            ]);
        }
    }
}
