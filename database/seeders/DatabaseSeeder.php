<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            AnalyticSeeder::class
        ]);

        User::factory()->create([
            'name' => 'Developer Omar',
            'role' => 'admin',
            'email' => 'admin@gramerdokan.com.bd',
            'password' => Hash::make('password'),
        ]);
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            PoliceStationSeeder::class,
            ZipcodeSeeder::class,
            AreaKeywordSeeder::class,
            SegmentSeeder::class,
            LandingPageSeeder::class,
        ]);
    }
}
