<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['id' => 1, 'name' => 'Dhaka', 'slug' => 'dhaka', 'country_id' => 1],
            ['id' => 2, 'name' => 'Chattogram', 'slug' => 'chattogram', 'country_id' => 1],
            ['id' => 3, 'name' => 'Rajshahi', 'slug' => 'rajshahi', 'country_id' => 1],
            ['id' => 4, 'name' => 'Khulna', 'slug' => 'khulna', 'country_id' => 1],
            ['id' => 5, 'name' => 'Barishal', 'slug' => 'barishal', 'country_id' => 1],
            ['id' => 6, 'name' => 'Sylhet', 'slug' => 'sylhet', 'country_id' => 1],
            ['id' => 7, 'name' => 'Rangpur', 'slug' => 'rangpur', 'country_id' => 1],
            ['id' => 8, 'name' => 'Mymensingh', 'slug' => 'mymensingh', 'country_id' => 1],
        ];
        State::insert($states);


    }
}
