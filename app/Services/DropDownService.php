<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Nationality;
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


}