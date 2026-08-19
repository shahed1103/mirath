<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use Illuminate\Support\Str;
use App\Services\JaaSService;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * Create instant or scheduled meeting
     */
    public function create_meet(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:instant,scheduled'],

            'scheduled_date' => [
                'required_if:type,scheduled',
                'nullable',
                'date',
            ],

            'scheduled_time' => [
                'required_if:type,scheduled',
                'nullable',
                'date_format:H:i',
            ],
        ]);

        // Generate unique room name
        $roomName = 'mirath_' . Str::random(10);

        $meeting = Meeting::create([
            'title' => $request->title,
            'description' => $request->description,
            'room_name' => $roomName,

            'meeting_link' => null,

            'type' => $request->type,

            'scheduled_date' => $request->type === 'scheduled'
                ? $request->scheduled_date
                : null,

            'scheduled_time' => $request->type === 'scheduled'
                ? $request->scheduled_time
                : null,

            'created_by' => auth()->id(),
        ]);

        $meeting->update([
            'meeting_link' => 'mirath://meeting/' . $meeting->id,
        ]);

        return response()->json([
            'status' => 1,

            'data' => [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'description' => $meeting->description,

                'meeting_link' => $meeting->meeting_link,

                'room_name' => $meeting->room_name,

                'type' => $meeting->type,

                'scheduled_date' => $meeting->scheduled_date,
                'scheduled_time' => $meeting->scheduled_time,
            ],

            'message' => $meeting->type === 'scheduled'
                ? 'meeting scheduled successfully'
                : 'meeting created successfully',
        ]);
    }

    /**
     * Join meeting and generate JaaS JWT
     */
    public function join_meet($meetingId, JaaSService $jaasService){
        $meeting = Meeting::find($meetingId);

        if (!$meeting) {
            return response()->json([
                'status' => 0,
                'message' => 'Meeting not found',
            ], 404);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Unauthenticated',
            ], 401);
        }

        /*
        * Scheduled meetings:
        * Allow joining 15 minutes before the scheduled time.
        * Access expires 1 hour and 10 minutes after the scheduled time.
        */
        if ($meeting->type === 'scheduled') {

            $scheduledAt = Carbon::parse(
                $meeting->scheduled_date . ' ' . $meeting->scheduled_time
            );

            $joinFrom = $scheduledAt->copy()->subMinutes(15);

            $joinUntil = $scheduledAt->copy()->addHour()->addMinutes(10);

            // Too early
            if (now()->lt($joinFrom)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'The meeting has not started yet.',
                    'data' => [
                        'join_available_from' => $joinFrom->toDateTimeString(),
                    ],
                ], 403);
            }

            // Meeting expired
            if (now()->gt($joinUntil)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'The meeting has expired.',
                    'data' => [
                        'expired_at' => $joinUntil->toDateTimeString(),
                    ],
                ], 410);
            }
        }

        // Meeting creator is moderator
        $isModerator = $meeting->created_by == $user->id;

        // Generate JWT
        $token = $jaasService->generateToken(
            roomName: $meeting->room_name,
            userId: $user->id,
            userName: $user->name,
            userEmail: $user->email,
            moderator: $isModerator
        );

        return response()->json([
            'status' => 1,

            'data' => [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'room_name' => $meeting->room_name,

                'jitsi_room' => config('services.jaas.app_id')
                    . '/' . $meeting->room_name,

                'jitsi_token' => $token,

                'server_url' => 'https://8x8.vc',
            ],

            'message' => 'Meeting joined successfully',
        ]);
    }

    /**
     * Delete scheduled meeting
     */
    public function delete_meet($id)
    {
        $meeting = Meeting::find($id);

        if (!$meeting) {
            return response()->json([
                'status' => 0,
                'message' => 'Meeting not found',
            ], 404);
        }

        if ($meeting->type !== 'scheduled') {
            return response()->json([
                'status' => 0,
                'message' => 'Only scheduled meetings can be deleted',
            ], 400);
        }

        $meeting->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Scheduled meeting deleted successfully',
        ]);
    }
}