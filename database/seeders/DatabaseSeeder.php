<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            NationalitySeeder::class,
            RolesPermissionsSeeder::class,
            ClassificationSeeder::class,
            FeaturesSeeder::class,
            LevelSeeder::class,
            BookSeeder::class,
            ChapterSeeder::class,
            ChapterContentSeeder::class,
            QuestionSeeder::class,
            QuestionHistorySeeder::class,
            OpenQuestionSeeder::class,
            LibraryBookSeeder::class,
            CartItemSeeder::class,
            ExamSeeder::class,

        ]);

    }
    
}
