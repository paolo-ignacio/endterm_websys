<?php

namespace App\Http\Controllers;

use App\Models\Attencdance;
use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;

class AttencdanceController extends Controller
{
    public function store(Request $request)
    {
        $idNumber = $request->input('id_number');
        $employee = Employee::where('id_number', $idNumber)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // Get today's date in local timezone (Philippine Time)
        $today = Carbon::today('Asia/Manila');
        $now = Carbon::now('Asia/Manila');  // Get current time in Philippine Time
        $hour = $now->hour;

        // Fetch or create today's attendance
        $attendance = Attencdance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => $today
        ]);

        // Check if it's after 5 PM and if both AM and PM time-in/out are null
        if ($hour >= 17 && !$attendance->am_time_in && !$attendance->am_time_out && !$attendance->pm_time_in && !$attendance->pm_time_out) {
            // Don't save anything if logged in after 5 PM and no session times are recorded
            return response()->json(['message' => 'Attendance recording is closed for today.'], 400);
        }

        if ($hour < 12) {
            // Morning session
            if (!$attendance->am_time_in) {
                $attendance->am_time_in = $now;  // Save Carbon instance directly
            } elseif (!$attendance->am_time_out) {
                $attendance->am_time_out = $now;  // Save Carbon instance directly
            }
        } else {
            // Afternoon session

            // If the employee forgot to log out in the morning
            if ($attendance->am_time_in && !$attendance->am_time_out) {
                // Automatically fill out missing fields in local time
                $attendance->am_time_out = Carbon::createFromTime(12, 0, 0, 'Asia/Manila'); // 12:00 PM
                $attendance->pm_time_in = Carbon::createFromTime(13, 0, 0, 'Asia/Manila');  // 1:00 PM
            }

            if (!$attendance->pm_time_in) {
                $attendance->pm_time_in = $now;  // Save Carbon instance directly
            } elseif (!$attendance->pm_time_out) {
                $attendance->pm_time_out = $now;  // Save Carbon instance directly
            }
        }

        $attendance->save();

        return response()->json([
            'message' => 'Attendance saved successfully.',
            'employee' => [
                'name' => $employee->name,
                'id_number' => $employee->id_number,
                'classification' => $employee->classification,
                'college' => $employee->college,
                'picture_path' => asset('storage/' . $employee->picture), // Adjust based on how you're storing images
            ],
            'attendance' => [
                'am_time_in' => optional($attendance->am_time_in)->format('h:i A'),
                'am_time_out' => optional($attendance->am_time_out)->format('h:i A'),
                'pm_time_in' => optional($attendance->pm_time_in)->format('h:i A'),
                'pm_time_out' => optional($attendance->pm_time_out)->format('h:i A'),
            ]
        ]);
    }
}
