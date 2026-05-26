<?php

namespace Database\Seeders;

use App\Models\Zipcode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZipcodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zipcodes = [
            ['id' => 1, 'name' => 'Demra / Sarulia', 'code' => '1361', 'slug' => '1361', 'police_station_id' => 11],
            ['id' => 2, 'name' => 'Gulshan', 'code' => '1212', 'slug' => '1212', 'police_station_id' => 12],
            ['id' => 3, 'name' => 'Banani', 'code' => '1213', 'slug' => '1213', 'police_station_id' => 13],
            ['id' => 4, 'name' => 'Dhanmondi', 'code' => '1209', 'slug' => '1209', 'police_station_id' => 14],
            ['id' => 5, 'name' => 'Uttara', 'code' => '1230', 'slug' => '1230', 'police_station_id' => 15],
            ['id' => 6, 'name' => 'Mirpur', 'code' => '1216', 'slug' => '1216', 'police_station_id' => 16],
            // Add more zipcodes as needed

        ];

        Zipcode::insert($zipcodes);

    }
}
