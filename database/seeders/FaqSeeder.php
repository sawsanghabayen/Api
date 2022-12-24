<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Faq::create([
            'question' => 'Question 1',
            'answer' => 'Answer 1',
          
        ]);
        Faq::create([
            'question' => 'Question 2',
            'answer' => 'Answer 2',
          
        ]);
        Faq::create([
            'question' => 'Question 3',
            'answer' => 'Answer 3',
          
        ]);
    }
}
