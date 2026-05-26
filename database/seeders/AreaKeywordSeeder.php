<?php

namespace Database\Seeders;

use App\Models\AreaKeyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areaKeywords = [
            ['name' => 'Dogair', 'slug' => 'Dogair', 'zipcode_id' => 1],
            ['name' => 'Sarulia', 'slug' => 'Sarulia', 'zipcode_id' => 1],
            ['name' => 'demra', 'slug' => 'demra', 'zipcode_id' => 1],

            // Add more area keywords as needed
        ];
        AreaKeyword::insert($areaKeywords);
    }
}
