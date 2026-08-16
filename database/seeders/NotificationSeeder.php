<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\BroadcastNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::skip(4)->take(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn(
                'No regular users found after skipping the first 4 admins.'
            );

            return;
        }
        $admin = User::find(3);

        /*
        |--------------------------------------------------------------------------
        | 1. New Book
        |--------------------------------------------------------------------------
        */
            BroadcastNotification::create([
                'title' => 'كتاب جديد',
                'body' => 'تمت إضافة كتاب جديد إلى المكتبة',
                'type' => 'new_book',
                'data' => [
                    'screen' => 'classification_details',
                    'classification_id' => 1,
                ],
            ]);
        /*
        |--------------------------------------------------------------------------
        | 2. New Chapter
        |--------------------------------------------------------------------------
        */
            BroadcastNotification::create([
                'title' => 'باب جديد',
                'body' => 'تمت إضافة باب جديد إلى أحد الكتب',
                'type' => 'new_chapter',
                'data' => [
                    'screen' => 'book_details',
                    'book_id' => 1,
                ],
            ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Request For Book Redemption
        |--------------------------------------------------------------------------
        |
        */

        if ($admin) {

            foreach ($users as $user) {

                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'طلب تبديل كتاب',
                    'body' => "هنالك طلب تبديل من المستخدم {$user->name} من أجل مجموعة كتب",
                    'type' => 'request_for_book_redemption',
                    'data' => [
                        'screen' => 'redemption_request_page',
                    ],
                    'is_read' => false,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Confirm Book Redemption
        |--------------------------------------------------------------------------
        |        |
        */

        foreach ($users as $user) {

            Notification::create([
                'user_id' => $user->id,
                'title' => 'موافقة على طلب تبديل الكتاب',
                'body' => 'تمت الموافقة على طلب تبديل الكتاب، يرجى مراجعة المكتبة في البرامكة، بجانب مشفى التوليد، خلال أوقات الدوام لإتمام عملية استلام الكتب من الساعة 10:00 صباحًا حتى 5:00 مساءً.',
                'type' => 'confirm_book_redemption',
                'data' => [],
                'is_read' => false,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Completed Book
        |--------------------------------------------------------------------------
        */

        foreach ($users as $user) {

            Notification::create([
                'user_id' => $user->id,
                'title' => "مبروك {$user->name}",
                'body' => 'لقد أتممت دراسة كتاب بنجاح.',
                'type' => 'completed_book',
                'data' => [],
                'is_read' => false,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Study Plan Reminder
        |--------------------------------------------------------------------------
        */

        foreach ($users as $user) {

            Notification::create([
                'user_id' => $user->id,
                'title' => 'وقت الدراسة 📚',
                'body' => 'حان وقت الدراسة! لديك مهام دراسية مجدولة اليوم.',
                'type' => 'study_reminder',
                'data' => [
                    'screen' => 'study_plan',
                ],
                'is_read' => false,
            ]);
        }
        
    }
}