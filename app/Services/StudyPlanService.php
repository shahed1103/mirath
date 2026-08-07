<?php

namespace App\Services;


use App\Models\StudyPlan;
use Carbon\Carbon;
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
            /*
             * 7- تحديث تاريخ النهاية
             */
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

private function attachStudyDays(
    StudyPlan $studyPlan,
    array $studyDays
): void {

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

}

