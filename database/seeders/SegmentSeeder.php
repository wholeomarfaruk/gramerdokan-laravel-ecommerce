<?php

namespace Database\Seeders;

use App\Models\Segment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $segments = [
            ['name' => 'Men', 'slug' => 'men', 'description' => 'Men\'s clothing', 'is_active' => true],
            ['name' => 'Women', 'slug' => 'women', 'description' => 'Women\'s clothing', 'is_active' => true],
           
        ];

          Segment::insert($segments);

    }
}
