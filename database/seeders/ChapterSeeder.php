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
        $status_ids = [1,1,1,1];
        $order_number = [1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4];


        for ($i=0; $i < 16 ; $i++) {
            Chapter::query()->create([
            'title' => $titles[$i % count($titles)],
            'book_id' => $book_ids[$i % count($book_ids)],
            'status_id' => $status_ids[$i % count($status_ids)],
            'order_number' => $order_number[$i],
            ]); }
    }
}
