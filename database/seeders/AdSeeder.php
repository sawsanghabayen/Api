<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ad::create([
            'name' => 'Mercides',
            'description' => 'Lorem Ipsum Lorem .',
            'price' => 20,
            'type' => 'N',
            'category_id' => '1',
            'user_id' => '1',
        ]);
        Ad::create([
            'name' => 'sofa',
            'description' => 'Lorem Ipsum Lorem .',
            'price' => 200,
            'type' => 'N',
            'category_id' => '2',
            'user_id' => '1',
        ]);
        Ad::create([
            'name' => 'Toyota',
            'description' => 'Lorem Ipsum Lorem .',
            'price' => 20,
            'type' => 'N',
            'category_id' => '1',
            'user_id' => '1',
        ]);
    }
}
