<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = ['إمكانية التواصل مع بوت ذكي' , 'إمكانية إنشاء جلسات جماعية' , 'إمكانية إنشاء خطة شخصية' , 'إمكانية إنشاء تلخيص'];

        for ($i=0; $i < 4 ; $i++) {
            Feature::query()->create([
           'feature' => $features[$i] ,
            ]);
        }
    }
    
}
