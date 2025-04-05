<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentMonth = $request->input('month', Carbon::now()->month);
        $currentYear = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $today = Carbon::now()->startOfDay();

        $stats = [
            'Hadir' => 0,
            'Sakit' => 0,
            'Izin' => 0,
            'Absen' => 0,
            'Terlambat' => 0,
        ];

        $dates = [];

        // Data keterlambatan
        $stats['Terlambat'] = AttendanceLog::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'late')
            ->count();

        // Proses harian
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');

            $attendance = AttendanceLog::where('user_id', $user->id)
                ->whereDate('date', $formattedDate)
                ->first();

            $leave = LeaveRequest::where('user_id', $user->id)
                ->whereDate('start_date', '<=', $formattedDate)
                ->whereDate('end_date', '>=', $formattedDate)
                ->first();

            $status = '-';
            $leaveData = null;

            if ($date <= $today) {
                if ($leave) {
                    $status = $leave->type === 'sick' ? 'S' : 'I';
                    $stats[$leave->type === 'sick' ? 'Sakit' : 'Izin']++;
                    $leaveData = $leave;
                } elseif ($attendance) {
                    $status = 'H';
                    $stats['Hadir']++;
                } else {
                    $status = 'A';
                    $stats['Absen']++;
                }
            }

            $dates[$formattedDate] = [
                'date' => $formattedDate,
                'status' => $status,
                'day' => $date->day,
                'leave' => $leaveData
            ];
        }

        return view('employee.attendance-history', [
            'stats' => $stats,
            'dates' => $dates,
            'currentMonthName' => $startDate->isoFormat('MMMM Y'),
            'selectedMonth' => $startDate->format('Y-m')
        ]);
    }

    public function getLeaveRequest($date)
    {
        try {
            $formattedDate = Carbon::parse($date)->format('Y-m-d');

            $leave = LeaveRequest::where('user_id', Auth::id())
                ->whereDate('start_date', '<=', $formattedDate)
                ->whereDate('end_date', '>=', $formattedDate)
                ->first();

            if (!$leave) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }

            return response()->json([
                'type' => ($leave->type == 'sick') ? 'Sakit' : (($leave->type == 'personal') ? 'Izin' : 'Lainnya'),
                'start_date' => Carbon::parse($leave->start_date)->format('d/m/Y'),
                'end_date' => Carbon::parse($leave->end_date)->format('d/m/Y'),
                'reason' => $leave->reason ?? '-',
                'attachment' => $leave->attachment ? asset("storage/{$leave->attachment}") : null
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan server', 'message' => $e->getMessage()], 500);
        }
    }
}
