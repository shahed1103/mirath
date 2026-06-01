<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = ['مغلق' , 'مفتوح'];

        for ($i=0; $i < 2 ; $i++) {
            Status::query()->create([
           'status' => $status[$i] ,
            ]);
        }
    }
}
