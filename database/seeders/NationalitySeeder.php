<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Nationality;


class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = ['Syrian' , 'Lebanese' , 'Palestinian' , 'South Sudanese' , 'Spanish' , 'Romanian' , 'Russian' , 'Tunisian'];

        for ($i=0; $i < 8 ; $i++) {
            Nationality::query()->create([
           'nationality' => $nationalities[$i] ,
            ]); }
    }
}
