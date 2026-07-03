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
        $order_number = [1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4];
        $start_page = [1,3,5,8,10,15,19,20,49,50,59,67,80,90,100,102];
        $end_page = [1,3,5,8,10,15,19,20,49,50,59,67,80,90,100,102];


        for ($i=0; $i < 16 ; $i++) {
            Chapter::query()->create([
            'title' => $titles[$i % count($titles)],
            'book_id' => $book_ids[$i % count($book_ids)],
            'order_number' => $order_number[$i],
            'start_page'=> $start_page[$i],
            'end_page' => $end_page[$i]+1,
            ]); }
    }
}
