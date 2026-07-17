<?php

namespace App\Services;
use App\Models\User;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserAdminService {

    public function getAllUsers(): array {
        $users = User::select('id', 'name', 'nick_name', 'email', 'age')
            ->with('latestExam:id,exams.user_id,correct_answers')
            ->role('Client')
            ->orderBy('created_at', 'desc')
            ->get()
                    ->map(function ($user) {

            return [
                'id' => $user->id,
                'name' => $user->name,
                'nick_name' => $user->nick_name,
                'email' => $user->email,
                'age' => $user->age,

                'last_exam_result' => $user->latestExam
                    ? ($user->latestExam->correct_answers*100)/25
                    : 0,
            ];
        })
        ->toArray();

        return [
            'data' => $users,
            'message' => 'All users retrieved successfully.'
        ];
    }

    public function getAllFeedbacks(): array {
        $feedbacks = Feedback::with(['user:id,name,nick_name,email'])
            ->select('id', 'feedback', 'created_at', 'user_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($feedback) {
                return [
                    'id' => $feedback->id,
                    'feedback' => $feedback->feedback,
                    'created_at' => $feedback->created_at->format('Y-m-d'),
                    'user' => $feedback->user ? [
                        'user_id' => $feedback->user->id,
                        'name' => $feedback->user->name,
                        'nick_name' => $feedback->user->nick_name,
                        'email' => $feedback->user->email,
                    ] : null,
                ];
            })
            ->toArray();

        return [
            'data' => $feedbacks,
            'message' => 'All feedbacks retrieved successfully.'
        ];
    }

    private function studentsQuery(){
        return User::role('client');
    }

    private function allStudentsNumber(): int{
        return (clone $this->studentsQuery())->count();
    }

    private function newStudentsThisMonth(): int{
        return (clone $this->studentsQuery())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function studentsByNationality(){
        $totalStudents = $this->allStudentsNumber();

        return (clone $this->studentsQuery())
            ->select(
                'nationality_id',
                DB::raw('COUNT(*) as total')
            )
            ->with('nationality:id,nationality')
            ->groupBy('nationality_id')
            ->get()
            ->map(function ($item) use ($totalStudents) {

                return [

                    'nationality' => optional($item->nationality)->nationality ?? 'Unknown',

                    'students_count' => $item->total,

                    'percentage' => $totalStudents == 0
                        ? 0
                        : round(($item->total / $totalStudents) * 100, 2),

                ];
            });
    }

    private function studentsByAge(): array{
        return [

            [
                'range' => '10 - 15',
                'count' => (clone $this->studentsQuery())
                    ->whereBetween('age', [10, 15])
                    ->count(),
            ],

            [
                'range' => '16 - 20',
                'count' => (clone $this->studentsQuery())
                    ->whereBetween('age', [16, 20])
                    ->count(),
            ],

            [
                'range' => '21 - 25',
                'count' => (clone $this->studentsQuery())
                    ->whereBetween('age', [21, 25])
                    ->count(),
            ],

            [
                'range' => '26+',
                'count' => (clone $this->studentsQuery())
                    ->where('age', '>=', 26)
                    ->count(),
            ],

        ];
    }

    public function userStatisticsOverview(): array{

        $data = [
            'total_students' => $this->allStudentsNumber(),

            'new_students_this_month' => $this->newStudentsThisMonth(),

            'students_by_nationality' => $this->studentsByNationality(),

            'students_by_age' => $this->studentsByAge(),
        ];

        return [
            'data' => $data,
            'message' => 'user statices retrived successfully'
        ];
    }

}