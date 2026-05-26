<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{

    public function run(): void
    {



      LandingPage::updateOrCreate(
            ['view_file' => 'templates.landingpages.page1'],

            [
                'name' => 'Product Landing Page 1',
                'view_file' => 'templates.landingpages.page1',
                'status' => true,
                'json_data' => file_get_contents(base_path('resources/views/templates/landingpages/page1.json')),
                'version' => json_decode(file_get_contents(base_path('resources/views/templates/landingpages/page1.json')))->version,
            ]
        );
      LandingPage::updateOrCreate(
            ['view_file' => 'templates.landingpages.page2'],

            [
                'name' => 'Product Landing with two video',
                'view_file' => 'templates.landingpages.page2',
                'status' => true,
                'json_data' => file_get_contents(base_path('resources/views/templates/landingpages/page2.json')),
                'version' => json_decode(file_get_contents(base_path('resources/views/templates/landingpages/page2.json')))->version,
            ]
        );

      LandingPage::updateOrCreate(
            ['view_file' => 'templates.landingpages.season_fresh_mango'],

            [
                'name' => 'Season Fresh Mango Landing Page',
                'view_file' => 'templates.landingpages.season_fresh_mango',
                'status' => true,
                'json_data' => file_get_contents(base_path('resources/views/templates/landingpages/season_fresh_mango.json')),
                'version' => json_decode(file_get_contents(base_path('resources/views/templates/landingpages/season_fresh_mango.json')))->version,
            ]
        );

    }
}
