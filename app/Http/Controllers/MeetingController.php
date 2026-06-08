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
    ]);

    $meeting = Meeting::create([
        'title' => $request->title,
        'description' => $request->description,
        'room_id' => Str::uuid(),
        'created_by' => auth()->id(),
    ]);

    return response()->json([
        'message' => 'Meeting created',
        'meeting' => $meeting,
    ]);
}
}
