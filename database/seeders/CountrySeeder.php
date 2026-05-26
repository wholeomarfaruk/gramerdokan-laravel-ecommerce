<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Bangladesh', 'short_code' => 'BD', 'code' => '+88', 'slug' => 'bangladesh'],
            // Add more countries as needed
        ];
        Country::insert($countries);
    }
}
