<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $cities = [
    // Dhaka Division (state_id = 1)
    ['id' => 1, 'name' => 'Dhaka', 'slug' => 'dhaka', 'state_id' => 1],
    ['id' => 2, 'name' => 'Gazipur', 'slug' => 'gazipur', 'state_id' => 1],
    ['id' => 3, 'name' => 'Kishoreganj', 'slug' => 'kishoreganj', 'state_id' => 1],
    ['id' => 4, 'name' => 'Manikganj', 'slug' => 'manikganj', 'state_id' => 1],
    ['id' => 5, 'name' => 'Munshiganj', 'slug' => 'munshiganj', 'state_id' => 1],
    ['id' => 6, 'name' => 'Narayanganj', 'slug' => 'narayanganj', 'state_id' => 1],
    ['id' => 7, 'name' => 'Narsingdi', 'slug' => 'narsingdi', 'state_id' => 1],
    ['id' => 8, 'name' => 'Tangail', 'slug' => 'tangail', 'state_id' => 1],
    ['id' => 9, 'name' => 'Faridpur', 'slug' => 'faridpur', 'state_id' => 1],
    ['id' => 10, 'name' => 'Gopalganj', 'slug' => 'gopalganj', 'state_id' => 1],
    ['id' => 11, 'name' => 'Madaripur', 'slug' => 'madaripur', 'state_id' => 1],
    ['id' => 12, 'name' => 'Rajbari', 'slug' => 'rajbari', 'state_id' => 1],
    ['id' => 13, 'name' => 'Shariatpur', 'slug' => 'shariatpur', 'state_id' => 1],

    // Chattogram Division (state_id = 2)
    ['id' => 14, 'name' => 'Chattogram', 'slug' => 'chattogram', 'state_id' => 2],
    ['id' => 15, 'name' => 'Cox\'s Bazar', 'slug' => 'coxs-bazar', 'state_id' => 2],
    ['id' => 16, 'name' => 'Bandarban', 'slug' => 'bandarban', 'state_id' => 2],
    ['id' => 17, 'name' => 'Rangamati', 'slug' => 'rangamati', 'state_id' => 2],
    ['id' => 18, 'name' => 'Khagrachhari', 'slug' => 'khagrachhari', 'state_id' => 2],
    ['id' => 19, 'name' => 'Noakhali', 'slug' => 'noakhali', 'state_id' => 2],
    ['id' => 20, 'name' => 'Feni', 'slug' => 'feni', 'state_id' => 2],
    ['id' => 21, 'name' => 'Lakshmipur', 'slug' => 'lakshmipur', 'state_id' => 2],
    ['id' => 22, 'name' => 'Brahmanbaria', 'slug' => 'brahmanbaria', 'state_id' => 2],
    ['id' => 23, 'name' => 'Cumilla', 'slug' => 'cumilla', 'state_id' => 2],
    ['id' => 24, 'name' => 'Chandpur', 'slug' => 'chandpur', 'state_id' => 2],

    // Rajshahi Division (state_id = 3)
    ['id' => 25, 'name' => 'Rajshahi', 'slug' => 'rajshahi', 'state_id' => 3],
    ['id' => 26, 'name' => 'Pabna', 'slug' => 'pabna', 'state_id' => 3],
    ['id' => 27, 'name' => 'Sirajganj', 'slug' => 'sirajganj', 'state_id' => 3],
    ['id' => 28, 'name' => 'Natore', 'slug' => 'natore', 'state_id' => 3],
    ['id' => 29, 'name' => 'Naogaon', 'slug' => 'naogaon', 'state_id' => 3],
    ['id' => 30, 'name' => 'Joypurhat', 'slug' => 'joypurhat', 'state_id' => 3],
    ['id' => 31, 'name' => 'Bogura', 'slug' => 'bogura', 'state_id' => 3],
    ['id' => 32, 'name' => 'Chapainawabganj', 'slug' => 'chapainawabganj', 'state_id' => 3],

    // Khulna Division (state_id = 4)
    ['id' => 33, 'name' => 'Khulna', 'slug' => 'khulna', 'state_id' => 4],
    ['id' => 34, 'name' => 'Bagerhat', 'slug' => 'bagerhat', 'state_id' => 4],
    ['id' => 35, 'name' => 'Chuadanga', 'slug' => 'chuadanga', 'state_id' => 4],
    ['id' => 36, 'name' => 'Jessore', 'slug' => 'jessore', 'state_id' => 4],
    ['id' => 37, 'name' => 'Jhenaidah', 'slug' => 'jhenaidah', 'state_id' => 4],
    ['id' => 38, 'name' => 'Kushtia', 'slug' => 'kushtia', 'state_id' => 4],
    ['id' => 39, 'name' => 'Magura', 'slug' => 'magura', 'state_id' => 4],
    ['id' => 40, 'name' => 'Meherpur', 'slug' => 'meherpur', 'state_id' => 4],
    ['id' => 41, 'name' => 'Narail', 'slug' => 'narail', 'state_id' => 4],
    ['id' => 42, 'name' => 'Satkhira', 'slug' => 'satkhira', 'state_id' => 4],

    // Barishal Division (state_id = 5)
    ['id' => 43, 'name' => 'Barishal', 'slug' => 'barishal', 'state_id' => 5],
    ['id' => 44, 'name' => 'Barguna', 'slug' => 'barguna', 'state_id' => 5],
    ['id' => 45, 'name' => 'Bhola', 'slug' => 'bhola', 'state_id' => 5],
    ['id' => 46, 'name' => 'Jhalokati', 'slug' => 'jhalokati', 'state_id' => 5],
    ['id' => 47, 'name' => 'Patuakhali', 'slug' => 'patuakhali', 'state_id' => 5],
    ['id' => 48, 'name' => 'Pirojpur', 'slug' => 'pirojpur', 'state_id' => 5],

    // Sylhet Division (state_id = 6)
    ['id' => 49, 'name' => 'Sylhet', 'slug' => 'sylhet', 'state_id' => 6],
    ['id' => 50, 'name' => 'Moulvibazar', 'slug' => 'moulvibazar', 'state_id' => 6],
    ['id' => 51, 'name' => 'Habiganj', 'slug' => 'habiganj', 'state_id' => 6],
    ['id' => 52, 'name' => 'Sunamganj', 'slug' => 'sunamganj', 'state_id' => 6],

    // Rangpur Division (state_id = 7)
    ['id' => 53, 'name' => 'Rangpur', 'slug' => 'rangpur', 'state_id' => 7],
    ['id' => 54, 'name' => 'Dinajpur', 'slug' => 'dinajpur', 'state_id' => 7],
    ['id' => 55, 'name' => 'Gaibandha', 'slug' => 'gaibandha', 'state_id' => 7],
    ['id' => 56, 'name' => 'Kurigram', 'slug' => 'kurigram', 'state_id' => 7],
    ['id' => 57, 'name' => 'Lalmonirhat', 'slug' => 'lalmonirhat', 'state_id' => 7],
    ['id' => 58, 'name' => 'Nilphamari', 'slug' => 'nilphamari', 'state_id' => 7],
    ['id' => 59, 'name' => 'Panchagarh', 'slug' => 'panchagarh', 'state_id' => 7],
    ['id' => 60, 'name' => 'Thakurgaon', 'slug' => 'thakurgaon', 'state_id' => 7],

    // Mymensingh Division (state_id = 8)
    ['id' => 61, 'name' => 'Mymensingh', 'slug' => 'mymensingh', 'state_id' => 8],
    ['id' => 62, 'name' => 'Jamalpur', 'slug' => 'jamalpur', 'state_id' => 8],
    ['id' => 63, 'name' => 'Netrokona', 'slug' => 'netrokona', 'state_id' => 8],
    ['id' => 64, 'name' => 'Sherpur', 'slug' => 'sherpur', 'state_id' => 8],
];

       City::insert($cities);
    }
}
