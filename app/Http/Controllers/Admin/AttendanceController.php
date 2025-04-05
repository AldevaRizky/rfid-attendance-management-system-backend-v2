<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Shift;
use App\Models\Division;
use App\Models\Position;
use App\Models\LeaveRequest;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->input('filter_type', 'month');
        $dateInput = $request->input('date', now()->format('Y-m-d'));
        $divisionId = $request->input('division');
        $positionId = $request->input('position');
        $search = $request->input('search');

        // Generate date range
        $dateRange = $this->generateDateRange($filterType, $dateInput);
        $dates = collect($dateRange)->pluck('date')->toArray();

        // Get users with filters
        $users = User::with(['division', 'position', 'attendance', 'leaves'])
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($positionId, fn($q) => $q->where('position_id', $positionId))
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('nip', 'like', "%$search%"))
            ->where('role', 'employee')
            ->get();

        // Prepare attendance data
        $attendanceData = $users->map(function($user) use ($dates) {
            $stats = ['H' => 0, 'T' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0, '-' => 0];
            $dateStatus = [];

            foreach ($dates as $date) {
                $status = $this->getDailyStatus($user, $date);
                $dateStatus[$date] = $status;

                if (array_key_exists($status, $stats)) {
                    $stats[$status]++;
                } else {
                    $stats['-']++;
                }
            }

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip,
                'division' => $user->division->name ?? '-',
                'position' => $user->position->name ?? '-',
                'dates' => $dateStatus,
                'stats' => $stats
            ];
        });

        return view('admin.attendance.index', [
            'attendanceData' => $attendanceData,
            'dateRange' => $dateRange,
            'divisions' => Division::all(),
            'positions' => Position::all(),
            'filters' => $request->all(),
            'filterType' => $filterType
        ]);
    }

    private function generateDateRange($filterType, $dateInput)
    {
        $start = Carbon::parse($dateInput);
        $range = [];

        switch ($filterType) {
            case 'month':
                $start->startOfMonth();
                $end = $start->copy()->endOfMonth();
                break;
            case 'week':
                $start->startOfWeek();
                $end = $start->copy()->endOfWeek();
                break;
            case 'day':
                $start->startOfDay();
                $end = $start->copy();
                break;
            default:
                $start->startOfMonth();
                $end = $start->copy()->endOfMonth();
        }

        while ($start <= $end) {
            $range[] = [
                'date' => $start->format('Y-m-d'),
                'is_weekend' => $start->isWeekend(),
                'is_sunday' => $start->isSunday()
            ];
            $start->addDay();
        }

        return $range;
    }

    private function getDailyStatus($user, $date)
    {
        $currentDate = Carbon::parse($date);

        if ($currentDate->isSunday()) {
            return 'L';
        }

        if ($currentDate->isSaturday()) {
            return 'L';
        }

        // Check leaves
        $leave = LeaveRequest::where('user_id', $user->id)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();

        if ($leave) {
            return $leave->type === 'sick' ? 'S' : 'I';
        }

        // Check attendance
        $attendance = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($attendance) {
            return $attendance->status === 'late' ? 'T' : 'H';
        }

        return $currentDate->isFuture() ? '-' : 'A';
    }

    public function getLeaveDetails($userId, $date)
    {
        try {
            $leave = LeaveRequest::where('user_id', $userId)
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->firstOrFail();

            return response()->json([
                'type' => $leave->type === 'sick' ? 'Sakit' : 'Izin',
                'start_date' => \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y'),
                'end_date' => \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y'),
                'reason' => $leave->reason ?? '-',
                'attachment' => $leave->attachment ? asset("storage/{$leave->attachment}") : null
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }

    public function exportPDF(Request $request)
    {
        // Ambil data sebagai array
        $data = $this->index($request)->getData();

        // Pastikan data ada sebelum diakses
        if (!isset($data['attendanceData']) || !isset($data['dateRange'])) {
            abort(500, 'Invalid attendance data');
        }

        $pdf = PDF::loadView('admin.attendance.export', [
            'attendanceData' => $data['attendanceData'],
            'dateRange' => $data['dateRange'],
            'startDate' => Carbon::parse($data['dateRange'][0]['date']),
            'endDate' => Carbon::parse(end($data['dateRange'])['date'])
        ])->setPaper('A4', 'landscape');

        return $pdf->download('laporan-absensi-'.now()->format('YmdHis').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        // Ambil data seperti di tampilan UI
        $data = $this->index($request)->getData();

        // Pastikan data tetap ada meskipun kosong
        if (!isset($data['attendanceData']) || !isset($data['dateRange'])) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        return Excel::download(new AttendanceExport($data['attendanceData'], $data['dateRange']), 'laporan-absensi.xlsx');
    }
}
