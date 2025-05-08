<?php

namespace App\Http\Controllers;

use App\Models\Attencdance;
use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
class AttencdanceController extends Controller
{
    public function store(Request $request)
    {
        $idNumber = $request->input('id_number');
        $employee = Employee::where('id_number', $idNumber)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $today = Carbon::today('Asia/Manila');
        $now = Carbon::now('Asia/Manila');
        $hour = $now->hour;

        $attendance = Attencdance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => $today
        ]);

        if ($hour >= 17 && !$attendance->am_time_in && !$attendance->am_time_out && !$attendance->pm_time_in && !$attendance->pm_time_out) {
            return response()->json(['message' => 'Attendance recording is closed for today.'], 400);
        }

        if ($hour < 12) {
            if (!$attendance->am_time_in) {
                $attendance->am_time_in = $now;
            } elseif (!$attendance->am_time_out) {
                $attendance->am_time_out = $now;
            }
        } else {
            if ($attendance->am_time_in && !$attendance->am_time_out) {
                $attendance->am_time_out = Carbon::createFromTime(12, 0, 0, 'Asia/Manila');
                $attendance->pm_time_in = Carbon::createFromTime(13, 0, 0, 'Asia/Manila');
            }

            if (!$attendance->pm_time_in) {
                $attendance->pm_time_in = $now;
            } elseif (!$attendance->pm_time_out) {
                $attendance->pm_time_out = $now;
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
                'picture_path' => asset('images/' . $employee->picture),
            ],
            'attendance' => [
                'am_time_in' => optional($attendance->am_time_in)->format('h:i A'),
                'am_time_out' => optional($attendance->am_time_out)->format('h:i A'),
                'pm_time_in' => optional($attendance->pm_time_in)->format('h:i A'),
                'pm_time_out' => optional($attendance->pm_time_out)->format('h:i A'),
            ]
        ]);
    }

    public function viewMonthlyReport(Request $request)
    {
        $role = $request->input('role');
        $month = $request->input('month', 4); // Default to April if no month is selected
    
        $employees = Employee::when($role, function ($query, $role) {
            return $query->where('classification', $role);
        })->get();
    
        $attendanceData = [];
    
        foreach ($employees as $employee) {
            $attendances = Attencdance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->get();
    
            $totalUndertimeMinutes = 0;
            $absentDates = [];
    
            foreach ($attendances as $attendance) {
                $totalUndertimeMinutes += $this->calculateUndertime($attendance);
                if ($this->calculateAbsence($attendance)) {
                    $absentDates[] = \Carbon\Carbon::parse($attendance->date)->toDateString();
                }
            }
    
            $absenceRanges = $this->formatAbsenceDates($absentDates);
    
            $attendanceData[] = [
                'name' => $employee->name,
                'undertime' => $this->formatMinutesToHoursMinutes($totalUndertimeMinutes),
                'absences' => $absenceRanges
            ];
        }
    
        // ✅ PAGINATION FOR ARRAY DATA
        $page = $request->input('page', 1);
        $perPage = 10;
        $collection = collect($attendanceData);
        $paginatedRecords = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    
        return view('scan.dtr', ['records' => $paginatedRecords]);
    }
    private function calculateUndertime($attendance)
    {
        // ✅ If fully absent, no undertime should be counted
        if (
            !$attendance->am_time_in && !$attendance->am_time_out &&
            !$attendance->pm_time_in && !$attendance->pm_time_out
        ) {
            return 0;
        }
    
        $totalUndertime = 0;
    
        // AM session
        if ($attendance->am_time_in && $attendance->am_time_out) {
            $amStart = Carbon::parse($attendance->am_time_in);
            $amEnd = Carbon::parse($attendance->am_time_out);
            $workedMinutes = $amStart->diffInMinutes($amEnd);
            $undertime = max(0, 240 - $workedMinutes);
            $totalUndertime += $undertime;
        } elseif ($attendance->am_time_in || $attendance->am_time_out) {
            // Partial scan = full AM undertime
            $totalUndertime += 240;
        }
    
        // PM session
        if ($attendance->pm_time_in && $attendance->pm_time_out) {
            $pmStart = Carbon::parse($attendance->pm_time_in);
            $pmEnd = Carbon::parse($attendance->pm_time_out);
            $workedMinutes = $pmStart->diffInMinutes($pmEnd);
            $undertime = max(0, 240 - $workedMinutes);
            $totalUndertime += $undertime;
        } elseif ($attendance->pm_time_in || $attendance->pm_time_out) {
            // Partial scan = full PM undertime
            $totalUndertime += 240;
        }
    
        return $totalUndertime;
    }
    private function calculateAbsence($attendance)
    {
        return !$attendance->am_time_in && !$attendance->am_time_out &&
               !$attendance->pm_time_in && !$attendance->pm_time_out;
    }

    private function formatMinutesToHoursMinutes($minutes)
    {
        if ($minutes === 0) {
        return '';
    }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return "{$hours} hrs. & {$mins} mins";
    }

    private function formatAbsenceDates(array $dates)
    {
        if (empty($dates)) return null;

        sort($dates);
        $ranges = [];
        $start = $end = Carbon::parse($dates[0]);

        for ($i = 1; $i < count($dates); $i++) {
            $current = Carbon::parse($dates[$i]);

            if ($current->diffInDays($end) == 1) {
                $end = $current;
            } else {
                $ranges[] = $this->formatRange($start, $end);
                $start = $end = $current;
            }
        }

        $ranges[] = $this->formatRange($start, $end);
        return implode(', ', $ranges);
    }

    private function formatRange($start, $end)
    {
        if ($start->equalTo($end)) {
            return $start->format('M. j, Y');
        } else {
            return $start->format('M. j') . '–' . $end->format('j, Y');
        }
    }
}
