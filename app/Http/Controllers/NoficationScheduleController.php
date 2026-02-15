<?php

namespace App\Http\Controllers;

use App\Models\NotificationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoficationScheduleController extends Controller
{
    public function update(Request $request, $id)
    {
        // Extra safety — gate by role
        if (Auth::user()->role !== 'nurse') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'scheduled_time' => ['required', 'date_format:H:i'],
            'is_active'      => ['required', 'boolean'],
        ]);

        $schedule = NotificationSchedule::findOrFail($id);

        $schedule->update([
            'scheduled_time' => $request->scheduled_time . ':00', // store as H:i:s
            'is_active'      => $request->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$schedule->display_name} schedule updated successfully.",
            'data'    => $schedule,
        ]);
    }
}
