<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = ['سهل' , 'متوسط' , 'صعب'];

        for ($i=0; $i < 3 ; $i++) {
            Level::query()->create([
           'level' => $levels[$i] ,
            ]); 
        }
    }
}
