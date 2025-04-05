<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Set default timezone to Indonesia (Jakarta)
        date_default_timezone_set('Asia/Jakarta');
    }

    public function admin()
    {
        $today = Carbon::today();
        $totalEmployees = User::where('role', 'employee')->count();

        // Get attendance statistics
        $present = AttendanceLog::whereDate('date', $today)->count();
        $late = AttendanceLog::whereDate('date', $today)->where('status', 'late')->count();
        $leave = LeaveRequest::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();
        $sick = LeaveRequest::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('type', 'sick')
            ->count();
        $absent = $totalEmployees - ($present + $leave);

        $stats = [
            'present' => $present,
            'late' => $late,
            'leave' => $leave - $sick,
            'sick' => $sick,
            'absent' => $absent,
            'total' => $totalEmployees
        ];

        // Get all users with their status
        $users = User::with(['division', 'position'])
            ->where('role', 'employee')
            ->get()
            ->map(function ($user) use ($today) {
                $attendance = AttendanceLog::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->first();

                $leave = LeaveRequest::where('user_id', $user->id)
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->first();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nip' => $user->nip,
                    'division' => $user->division->name ?? '-',
                    'position' => $user->position->name ?? '-',
                    'shift' => '-',
                    'status' => $this->determineStatus($attendance, $leave),
                    'check_in' => optional($attendance)->check_in_time ? Carbon::parse($attendance->check_in_time)->setTimezone('Asia/Jakarta') : '-',
                    'check_out' => optional($attendance)->check_out_time ? Carbon::parse($attendance->check_out_time)->setTimezone('Asia/Jakarta') : '-',
                ];
            });

        return view('dashboard.admin', [
            'users' => $users,
            'stats' => $stats,
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => User::where('role', 'employee')->where('status', 'active')->count(),
        ]);
    }

    private function determineStatus($attendance, $leave)
    {
        if ($leave) {
            return $leave->type === 'sick' ? 'sick' : 'leave';
        }

        if ($attendance) {
            return $attendance->status === 'late' ? 'late' : 'present';
        }

        return 'absent';
    }

    public function getLeaveDetails($userId)
    {
        $leave = LeaveRequest::where('user_id', $userId)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->first();

        if (!$leave) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'type' => ($leave->type == 'sick') ? 'Sakit' : (($leave->type == 'personal') ? 'Izin' : 'Lainnya'),
            'start_date' => Carbon::parse($leave->start_date)->format('d/m/Y'),
            'end_date' => Carbon::parse($leave->end_date)->format('d/m/Y'),
            'reason' => $leave->reason ?? '-',
            'attachment' => $leave->attachment ? asset("storage/{$leave->attachment}") : null
        ]);
    }


    public function employee()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Cek apakah user memiliki absensi hari ini
        $attendance = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Cek apakah user memiliki izin atau sakit hari ini
        $leave = LeaveRequest::where('user_id', $user->id)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        $status = $leave ? ($leave->type === 'sick' ? 'sick' : 'leave') : ($attendance ? 'present' : 'absent');

        return view('dashboard.employee', [
            'status' => $status,
            'check_in' => optional($attendance)->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i') : '-',
            'check_out' => optional($attendance)->check_out_time ? Carbon::parse($attendance->check_out_time)->format('H:i') : '-',
        ]);
    }
}
