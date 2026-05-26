<?php

namespace Database\Seeders;

use App\Models\PoliceStation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoliceStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $police_stations = [
            // Dhaka City (city_id = 1)
            ['id' => 1, 'name' => 'Ramna', 'slug' => 'ramna', 'city_id' => 1],
            ['id' => 2, 'name' => 'Tejgaon', 'slug' => 'tejgaon', 'city_id' => 1],
            ['id' => 3, 'name' => 'Dhanmondi', 'slug' => 'dhanmondi', 'city_id' => 1],
            ['id' => 4, 'name' => 'Gulshan', 'slug' => 'gulshan', 'city_id' => 1],
            ['id' => 5, 'name' => 'Banani', 'slug' => 'banani', 'city_id' => 1],
            ['id' => 6, 'name' => 'Mohammadpur', 'slug' => 'mohammadpur', 'city_id' => 1],
            ['id' => 7, 'name' => 'Uttara', 'slug' => 'uttara', 'city_id' => 1],
            ['id' => 8, 'name' => 'Mirpur', 'slug' => 'mirpur', 'city_id' => 1],
            ['id' => 9, 'name' => 'Pallabi', 'slug' => 'pallabi', 'city_id' => 1],
            ['id' => 10, 'name' => 'Keraniganj', 'slug' => 'keraniganj', 'city_id' => 1],
            ['id' => 11, 'name' => 'Demra', 'slug' => 'demra', 'city_id' => 1],
            ['id' => 12, 'name' => 'Savar', 'slug' => 'savar', 'city_id' => 1],
            ['id' => 13, 'name' => 'Dhamrai', 'slug' => 'dhamrai', 'city_id' => 1],
            ['id' => 14, 'name' => 'Nawabganj', 'slug' => 'nawabganj', 'city_id' => 1],
            ['id' => 15, 'name' => 'Keraniganj Industrial Area', 'slug' => 'keraniganj-industrial', 'city_id' => 1],

            // Chattogram City (city_id = 2)
            ['id' => 16, 'name' => 'Kotwali', 'slug' => 'kotwali', 'city_id' => 2],
            ['id' => 17, 'name' => 'Pahartali', 'slug' => 'pahartali', 'city_id' => 2],
            ['id' => 18, 'name' => 'Double Mooring', 'slug' => 'double-mooring', 'city_id' => 2],
            ['id' => 19, 'name' => 'Anwara', 'slug' => 'anwara', 'city_id' => 2],
            ['id' => 20, 'name' => 'Chandgaon', 'slug' => 'chandgaon', 'city_id' => 2],
        ];
        PoliceStation::insert($police_stations);

    }
}
