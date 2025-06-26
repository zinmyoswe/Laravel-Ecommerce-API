<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Posterslide;

class PosterslideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Posterslide::create([
            'posterslideimage' => 'https://example.com/banner.jpg',
            'posterslidename' => 'Big Summer Sale',
            'posterslidename2' => 'Up to 40% Off',
            'buttonname' => 'Shop Now',
            'part' => 1,
            'status' => 1,
        ]);
    }
}
