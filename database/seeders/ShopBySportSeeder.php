<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopBySportSeeder extends Seeder
{
    public function run()
    {
        $sports = [
            [
                'sportname' => 'Running',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/a3c971bc-bc0a-4c0c-8bdf-e807a3027e53/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'Football',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/e4695209-3f23-4a05-a9f9-d0edde31b653/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'BasketBall',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/38ed4b8e-9cfc-4e66-9ddd-02a52314eed9/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'Training and Gym',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/e36a4a2b-4d3f-4d1c-bc75-d6057b7cec87/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'Tennis',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/7ce96f81-bf80-45b9-918e-f2534f14015d/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'Yoga',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/6be55ac6-0243-42d6-87d0-a650074c658c/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'Skateboarding',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/608705dc-dea5-4450-b68f-e624cf1ed2a7/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'Dance',
                'image' => 'https://static.nike.com/a/images/f_auto/dpr_1.3,cs_srgb/h_435,c_limit/c779e4f6-7d91-46c3-9282-39155e0819e5/nike-just-do-it.jpg',
                'slide_active' => 0,
            ],
            [
                'sportname' => 'Golf',
                'image' => 'https://static.nike.com/a/images/w_1920,c_limit/3b1866f0-90c3-4a73-8f0f-acbec8a6a58a/the-best-cold-weather-golf-gear-by-nike.jpg',
                'slide_active' => 0,
            ],
        ];

        DB::table('shop_by_sports')->insert($sports);
    }
}
