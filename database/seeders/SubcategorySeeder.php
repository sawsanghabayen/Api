<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subcategory::create([
            'name' => 'Opel',
            'category_id' => '1',
           
        ]);
        Subcategory::create([
            'name' => 'Cars Sale',
            'category_id' => '1',
           
        ]);
        Subcategory::create([
            'name' => '2020',
            'category_id' => '1',
           
        ]);
    }
}
