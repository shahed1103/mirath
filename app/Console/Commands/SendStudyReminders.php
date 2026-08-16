<?php

namespace App\Console\Commands;

use App\Models\StudyPlan;
use App\Models\StudyTask;
use App\Models\StudyPlanReminderLog;
use App\Services\NotificationManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendStudyReminders extends Command
{
    protected $signature = 'study:send-reminders';

    protected $description = 'Send study reminders to users';

    public function __construct(
        private readonly NotificationManager $notificationManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $now = Carbon::now();

        $currentTime = $now->format('H:i');
        $today = $now->toDateString();

        $this->info("Checking study reminders at {$currentTime}");

        $plans = StudyPlan::query()
            ->where('status', StudyPlan::ACTIVE)
            ->whereNotNull('notification_time')
            ->get();

        foreach ($plans as $plan) {
            $notificationTime = Carbon::parse(
                $plan->notification_time
            )->format('H:i');

            if ($notificationTime !== $currentTime) {
                continue;
            }
            $alreadySent = StudyPlanReminderLog::query()
                ->where('study_plan_id', $plan->id)
                ->where('reminder_date', $today)
                ->exists();

            if ($alreadySent) {
                continue;
            }


            $tasks = StudyTask::query()
                ->where('study_plan_id', $plan->id)
                ->where('user_id', $plan->user_id)
                ->whereDate('task_date', $today)
                ->where('completed', false)
                ->with([
                    'book:id,title'
                ])
                ->get();

            if ($tasks->isEmpty()) {
                continue;
            }

            $totalPages = $tasks->sum('pages');

            $taskCount = $tasks->count();

            $bookTitles = $tasks
                ->pluck('book.title')
                ->filter()
                ->unique()
                ->values()
                ->implode('، ');

            $notificationRequest = new Request([
                'userId' => $plan->user_id,

                'title' => 'وقت الدراسة 📚',

                'body' => $totalPages > 0
                    ? "حان وقت الدراسة! لديك {$taskCount} مهام و {$totalPages} صفحة اليوم."
                    : "حان وقت الدراسة! لديك {$taskCount} مهام دراسية اليوم.",

                'type' => 'study_reminder',

                'data' => [
                    'screen'=>'study_plan',
                ]
            ]);

            try {

                $this->notificationManager
                    ->sendNotification($notificationRequest);

                StudyPlanReminderLog::create([
                    'study_plan_id' => $plan->id,
                    'user_id' => $plan->user_id,
                    'reminder_date' => $today,
                    'sent_at' => now(),
                ]);


                $this->info(
                    "Reminder sent for Study Plan #{$plan->id}"
                );

            } catch (\Throwable $e) {

                Log::error(
                    "Failed to send study reminder for Study Plan #{$plan->id}: "
                    . $e->getMessage()
                );

                $this->error(
                    "Failed for Study Plan #{$plan->id}: "
                    . $e->getMessage()
                );
            }
        }

        return self::SUCCESS;
    }
}