<?php

namespace App\Services;


use App\Models\StudyPlan;
use Carbon\Carbon;
use App\Models\StudyTask;
use Illuminate\Support\Facades\DB;

class StudyPlanService
{
    public function __construct(
        private readonly PlanCalculatorService $calculator,
        private readonly TaskGeneratorService $generator
    ) {
    }

    public function calculate(array $data): array
{
    return $this->calculator->calculate($data);
}

public function create(int $userId, array $data): array
    {
        return DB::transaction(function () use ($userId, $data) {

            /*
             * 1- حساب عدد الأيام أو الورد
             */
            $plan = $this->calculator->calculate($data);

            /*
             * 2- تحديد أول يوم دراسة
             */
            $startDate = $this->generator->resolveStartDate(
                $data['study_days']
            );

            /*
             * 3- إنشاء الخطة
             */
            $studyPlan = $this->createPlan(
                $userId,
                $data,
                $plan['data'],
                $startDate
            );

            /*
             * 4- حفظ الكتب
             */
            $this->attachBooks(
                $studyPlan,
                $data['book_ids']
            );

            /*
             * 5- حفظ أيام الدراسة
             */
            $this->attachStudyDays(
                $studyPlan,
                $data['study_days']
            );

            /*
             * 6- توليد المهام
             */
            $endDate = $this->generator->generate($studyPlan);

            $studyPlan->update([
                'end_date' => $endDate
            ]);


            return [

                'data' => $studyPlan->load([
    'books.book',
    'days'
]),
                'message' => 'تم إنشاء الخطة بنجاح.'

            ];

        });
    }


public function getTasksByRange(
    int $userId,
    string $fromDate,
    string $toDate
): array {

    $tasks = StudyTask::query()
        ->where('user_id', $userId)
        ->whereBetween('task_date', [
            $fromDate,
            $toDate
        ])
        ->with([
            'book:id,title,author_name,photo',
            'chapter:id,title,book_id,start_page,end_page',
        ])
        ->orderBy('task_date')
        ->orderBy('reading_order')
        ->get();

    $groupedTasks = $tasks
        ->groupBy(function ($task) {
            return $task->task_date->format('Y-m-d');
        })
        ->map(function ($dayTasks, $date) {

            return [
                'date' => $date,

                'tasks' => $dayTasks->map(function ($task) {

                    return [
                        'id' => $task->id,

                        'book_id' => $task->book_id,
                        'book_title' => $task->book?->title,

                        'chapter_id' => $task->chapter_id,
                        'chapter_title' => $task->chapter?->title,

                        'from_page' => $task->from_page,
                        'to_page' => $task->to_page,
                        'pages' => $task->pages,

                        'completed' => $task->completed,
                        'completed_at' => $task->completed_at,

                        'reading_order' => $task->reading_order,
                    ];

                })->values(),
            ];
        })
        ->values();

    return [
        'data' => $groupedTasks,
        'message' => 'تم جلب المهام بنجاح.'
    ];
}






private function createPlan(
    int $userId,
    array $requestData,
    array $planData,
    Carbon $startDate
): StudyPlan
{
    return StudyPlan::create([

        'user_id' => $userId,

        'plan_type' => $requestData['plan_type'],

        'daily_pages' => $planData['daily_pages'],

        'target_days' => $planData['target_days'],

        'notification_time' => $requestData['notification_time'],

        'offline' => $requestData['offline'],

        'start_date' => $startDate,

        'end_date' => null,

        'status' => StudyPlan::ACTIVE,

        'total_pages' => $planData['total_pages'],

        'total_books' => $planData['total_books']

    ]);
}


private function attachBooks(
    StudyPlan $studyPlan,
    array $bookIds
): void {

    $books = [];
//$studyDays = array_unique($studyDays);

    foreach ($bookIds as $bookId) {

        $books[] = [
            'book_id' => $bookId
        ];

    }

    $studyPlan
        ->books()
        ->createMany($books);
}

private function attachStudyDays(StudyPlan $studyPlan, array $studyDays): void {

    $days = [];
    $studyDays = array_unique($studyDays);
    sort($studyDays);
    foreach ($studyDays as $day) {

        $days[] = [
            'day_number' => $day
        ];

    }

    $studyPlan
        ->days()
        ->createMany($days);
}




public function completeTask( int $taskId  ): array {
        $userId = auth()->id();
        $task = StudyTask::where('id', $taskId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!$task->completed) {

            $task->update([
                'completed' => true,
                'completed_at' => now(),
            ]);
        }

        $progress = $this->getPlanProgress(
            $task->study_plan_id
        );

        return [
            'data' => [
                'task' => $task->fresh(),
                'progress' => $progress,
            ],
            'message' => 'تم إنجاز المهمة بنجاح.',
        ];
}


    // =====================================================
    // Get Plan Progress
    // =====================================================
public function getPlanProgress(): array {
    $userId = auth()->id();

    $studyPlans = StudyPlan::where('user_id', $userId)
        ->with('tasks')
        ->get();

    $totalPages = $studyPlans->sum(function ($studyPlan) {
        return $studyPlan->tasks->sum('pages');
    });

    $completedPages = $studyPlans->sum(function ($studyPlan) {
        return $studyPlan->tasks
            ->where('completed', true)
            ->sum('pages');
    });

    $percentage = $totalPages > 0
        ? round(($completedPages / $totalPages) * 100, 2)
        : 0;

    return [
        'total_pages' => $totalPages,
        'completed_pages' => $completedPages,
        'remaining_pages' => $totalPages - $completedPages,
        'percentage' => $percentage,
    ];
}
}

