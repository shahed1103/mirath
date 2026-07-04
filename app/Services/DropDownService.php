<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Nationality;
use App\Models\Level;
use Illuminate\Support\Facades\Cache;
use Exception;
use Throwable;

class DropDownService {

    public function getNationalities(): array{
        $nationalities = Cache::remember('nationalities_dropdown', 3600, function () {
            return Nationality::select('id', 'nationality')
                ->get()
                ->map(function ($nationality) {
                    return [
                        'id' => $nationality->id,
                        'nationality' => $nationality->nationality,
                    ];
                })
                ->toArray();
        });

        return [
            'nationalities' => $nationalities,
            'message' => 'all nationalities are retrieved successfully',
        ];
    }

    public function getLevels(): array {
        
        $levels = Cache::remember('levels_dropdown', 3600, function () {
            return Level::select('id', 'level')
                ->get()
                ->map(function ($level) {
                    return [
                        'id' => $level->id,
                        'level' => $level->level,
                    ];
                })
                ->toArray();
        });

        return [
            'levels' => $levels,
            'message' => 'all levels are retrieved successfully',
        ];
    }
    
}