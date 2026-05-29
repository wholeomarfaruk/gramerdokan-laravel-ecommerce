<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{

    public function run(): void
    {



      LandingPage::updateOrCreate(
            ['view_file' => 'templates.landingpages.seldom_zaynah_eid'],

            [
                'name' => 'Seldom — Zaynah Eid (Single Video)',
                'view_file' => 'templates.landingpages.seldom_zaynah_eid',
                'status' => true,
                'json_data' => file_get_contents(base_path('resources/views/templates/landingpages/seldom_zaynah_eid.json')),
                'version' => json_decode(file_get_contents(base_path('resources/views/templates/landingpages/seldom_zaynah_eid.json')))->version,
            ]
        );
      LandingPage::updateOrCreate(
            ['view_file' => 'templates.landingpages.seldom_zaynah_eid_v2'],

            [
                'name' => 'Seldom — Zaynah Eid v2 (Dual Video)',
                'view_file' => 'templates.landingpages.seldom_zaynah_eid_v2',
                'status' => true,
                'json_data' => file_get_contents(base_path('resources/views/templates/landingpages/seldom_zaynah_eid_v2.json')),
                'version' => json_decode(file_get_contents(base_path('resources/views/templates/landingpages/seldom_zaynah_eid_v2.json')))->version,
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
