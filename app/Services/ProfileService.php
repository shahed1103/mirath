<?php


namespace App\Services;
use App\Models\Exam;

class ProfileService {

public function getSuccessfulExamsCount(): array
{
    $count = Exam::where('user_id', auth()->id())
                ->where('success', true)
                ->count();

    return [
        'count' => $count,
        'message' => 'Successful exams count retrieved successfully'
    ];
}


}
