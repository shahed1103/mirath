<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meeting;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
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

        $roomName = 'tarahm_' . Str::random(10);

        $meeting = Meeting::create([
            'title' => $request->title,
            'description' => $request->description,
            'room_name' => $roomName,
            'meeting_link' => 'https://meet.jit.si/' . $roomName,
            'type' => $request->type,
            'scheduled_date' => $request->type === 'scheduled'
                ? $request->scheduled_date
                : null,
            'scheduled_time' => $request->type === 'scheduled'
                ? $request->scheduled_time
                : null,
            'created_by' => auth()->id(),
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
