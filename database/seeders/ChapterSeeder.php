<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {    
        $titles = ['سلوكي' , 'شرعي' , 'إصلاحي' , 'فكري'];
        $book_ids = [1,2,3,4];


        for ($i=0; $i < 4 ; $i++) {
            Chapter::query()->create([
           'title' => $titles[$i] ,
           'book_id' => $book_ids[$i] ,
            ]); }
    }
}
