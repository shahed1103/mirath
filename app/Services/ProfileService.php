<?php


namespace App\Services;
use App\Models\Exam;

use App\Models\LibraryBook;
use App\Http\Resources\LibraryBookResource;

class ProfileService {

public function getStudentStatistics(): array
{
    $successfulExams = Exam::where('user_id', auth()->id())
        ->where('success', true);
    $successfulExamsCount = (clone $successfulExams)->count();
    $averageCorrectAnswers = (clone $successfulExams)->avg('correct_answers');
    $averagePercentage = $averageCorrectAnswers
        ? round(($averageCorrectAnswers / 25) * 100, 2)
        : 0;
    return [
        'statistics' => [
            'successful_exams_count' => $successfulExamsCount,
            'average_percentage' => $averagePercentage,
        ],
        'message' => 'Student statistics retrieved successfully'
    ];
}


public function getMyPoints(): array
{
    $points = User::where('user_id', auth()->id())->get('points');
    return [
        'points' => $points,
        'message' => 'Student points retrieved successfully'
    ];
}


public function getAllLibraryBooks(): array
{
    $books = LibraryBook::all();

    return [
        'books' => LibraryBookResource::collection($books),
        'message' => 'Library books retrieved successfully'
    ];
}

}

