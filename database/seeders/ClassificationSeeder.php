<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classification;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classifications = ['سلوكي' , 'شرعي' , 'إصلاحي' , 'فكري'];
        $bio = ['سلوكي' , 'شرعي' , 'إصلاحي' , 'فكري'];


        for ($i=0; $i < 4 ; $i++) {
            Classification::query()->create([
           'classification' => $classifications[$i] ,
           'bio' => $bio[$i] ,
            ]); 
        }
    }
}
