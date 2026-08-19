<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Services\NotificationManager;

class SendMeetingReminders extends Command
{
    protected $signature = 'meetings:send-reminders';

    protected $description = 'Send meeting reminders one hour before scheduled meetings';

    public function __construct(
        private readonly NotificationManager $notificationManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $now = now()->startOfMinute();

        $targetTime = $now->copy()->addHour();

        $this->info('Current time: ' . $now->format('Y-m-d H:i'));
        $this->info('Looking for meetings at: ' . $targetTime->format('Y-m-d H:i'));

        $meetings = Meeting::query()
            ->where('type', 'scheduled')
            ->whereNull('reminder_sent_at')
            ->whereNotNull('scheduled_date')
            ->whereNotNull('scheduled_time')
            ->get();

        $this->info('Scheduled meetings found: ' . $meetings->count());

        foreach ($meetings as $meeting) {

            $scheduledAt = Carbon::parse(
                $meeting->scheduled_date . ' ' . $meeting->scheduled_time
            )->startOfMinute();

            $this->info(
                "Meeting #{$meeting->id} scheduled at: "
                . $scheduledAt->format('Y-m-d H:i')
            );

            /*
            * Compare only date + hour + minute
            */
            if ($scheduledAt->equalTo($targetTime)) {

                $this->info(
                    "Meeting #{$meeting->id} is due for reminder."
                );

                $notificationRequest = new Request([
                    'userId' => $meeting->created_by,

                    'title' => 'تذكير بالاجتماع',

                    'body' => "لديك اجتماع دراسة جماعية بعنوان {$meeting->title} بعد ساعة.",

                    'type' => 'meeting_reminder',

                    'data' => [
                        'screen' => 'meetings',
                        'meeting_id' => $meeting->id,
                        'meeting_link' => $meeting->meeting_link,
                        'scheduled_date' => $meeting->scheduled_date,
                        'scheduled_time' => $meeting->scheduled_time,
                    ],
                ]);

                $this->notificationManager
                    ->sendNotification($notificationRequest);

                $meeting->update([
                    'reminder_sent_at' => now(),
                ]);

                $this->info(
                    "Reminder sent for meeting #{$meeting->id}"
                );
            }
        }

        return self::SUCCESS;
    }
}