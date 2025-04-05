<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function create()
    {
        $existingLeave = LeaveRequest::where('user_id', Auth::id())
            ->where('end_date', '>=', now()->toDateString()) // Cek jika izin masih berlangsung
            ->first();

        return view('employee.leave_request.create', compact('existingLeave'));
    }

    public function store(Request $request)
    {
        // Cek apakah user sudah memiliki izin yang sedang berlangsung
        $existingLeave = LeaveRequest::where('user_id', Auth::id())
            ->where('end_date', '>=', now()->toDateString())
            ->first();

        if ($existingLeave) {
            return redirect()->route('employee.leave-request.create')->with('error', 'Anda sudah mengajukan izin hingga ' . $existingLeave->end_date);
        }

        // Validasi input
        $request->validate([
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Proses unggah file (jika ada)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_requests', 'public');
        }

        // Menyimpan pengajuan izin
        LeaveRequest::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('employee.dashboard')->with('success', 'Pengajuan izin berhasil dikirim!');
    }
}
