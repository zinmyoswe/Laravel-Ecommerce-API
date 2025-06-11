<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    public function run()
    {
        $option1 = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'];
        $option2 = [
            'US M 6 / W 7.5', 'US M 6.5 / W 8', 'US M 7 / W 8.5',
            'US M 7.5 / W 9', 'US M 8 / W 9.5', 'US M 8.5 / W 10',
            'US M 9 / W 10.5', 'US M 9.5 / W 11', 'US M 10 / W 11.5',
            'US M 10.5 / W 12', 'US M 11 / W 12.5', 'US M 11.5 / W 13',
            'US M 12 / W 13.5', 'US M 13 / W 14.5'
        ];
        $option3 = ['One Size'];

        foreach ($option1 as $size) {
            DB::table('sizes')->insert([
                'sizetype' => 'clothing',
                'sizevalue' => $size,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($option2 as $size) {
            DB::table('sizes')->insert([
                'sizetype' => 'shoes',
                'sizevalue' => $size,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($option3 as $size) {
            DB::table('sizes')->insert([
                'sizetype' => 'onesize',
                'sizevalue' => $size,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
