<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Shift;
use App\Models\RfidCard;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function processAttendance(Request $request)
    {
        $request->validate([
            'rfid_number' => 'required|string',
        ]);

        // Set timezone ke Indonesia (WIB)
        date_default_timezone_set('Asia/Jakarta');
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->copy()->startOfDay();

        // Cari kartu RFID dengan relasi user
        $rfidCard = RfidCard::with('user')->where('card_number', $request->rfid_number)->first();

        if (!$rfidCard) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak terdaftar di database'
            ], 404);
        }

        // Validasi status kartu
        if ($rfidCard->status === 'Blocked') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu ini diblokir dan tidak dapat digunakan untuk absensi'
            ], 403);
        }

        if ($rfidCard->status === 'Inactive') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu ini tidak aktif'
            ], 403);
        }

        // Validasi expired date - konversi ke Carbon jika belum
        $expiredDate = $rfidCard->expired_date ? Carbon::parse($rfidCard->expired_date) : null;

        if ($expiredDate && $now->gt($expiredDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu sudah kadaluarsa',
                'data' => [
                    'expired_date' => $expiredDate->format('Y-m-d'),
                    'current_date' => $now->format('Y-m-d')
                ]
            ], 403);
        }

        // Cari user berdasarkan RFID
        $user = $rfidCard->user;
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan untuk kartu ini'
            ], 404);
        }

        // Tentukan shift yang aktif sekarang
        $currentTime = $now->format('H:i:s');
        $shift = Shift::where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift yang aktif saat ini'
            ], 400);
        }

        // Cek apakah sudah ada attendance log hari ini
        $attendanceLog = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Jika belum ada log (check in)
        if (!$attendanceLog) {
            // Parse waktu shift dengan tanggal hari ini
            $shiftStart = Carbon::createFromFormat('Y-m-d H:i:s', $today->format('Y-m-d') . ' ' . $shift->start_time, 'Asia/Jakarta');
            $gracePeriodEnd = $shiftStart->copy()->addMinutes($shift->grace_period);
            $maxLateTime = $shiftStart->copy()->addMinutes($shift->max_late_time);

            // Debug log
            Log::debug('Attendance Check', [
                'now' => $now->format('Y-m-d H:i:s'),
                'shift_start' => $shiftStart->format('Y-m-d H:i:s'),
                'grace_period_end' => $gracePeriodEnd->format('Y-m-d H:i:s'),
                'max_late_time' => $maxLateTime->format('Y-m-d H:i:s'),
                'shift_id' => $shift->id,
                'shift_name' => $shift->name,
                'rfid_status' => $rfidCard->status,
                'rfid_expired' => $expiredDate ? $expiredDate->format('Y-m-d') : null
            ]);

            // Tentukan status
            if ($now->gt($maxLateTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melewati batas maksimal keterlambatan',
                    'data' => [
                        'max_late_time' => $maxLateTime->format('H:i:s'),
                        'current_time' => $now->format('H:i:s'),
                        'shift_name' => $shift->name
                    ]
                ], 400);
            }

            $status = $now->gt($gracePeriodEnd) ? 'late' : 'on time';

            // Buat log baru
            $attendanceLog = AttendanceLog::create([
                'user_id' => $user->id,
                'rfid_card_id' => $rfidCard->id,
                'shift_id' => $shift->id,
                'check_in_time' => $now,
                'date' => $today,
                'status' => $status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi masuk berhasil',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'nip' => $user->nip,
                        'division' => $user->division->name ?? '-',
                        'position' => $user->position->name ?? '-',
                        'photo_url' => $user->profile_photo_url,
                    ],
                    'attendance' => [
                        'check_in_time' => $now->format('H:i:s'),
                        'status' => $status == 'late' ? 'Terlambat' : 'Tepat Waktu',
                        'shift' => $shift->name,
                        'grace_period' => $shift->grace_period . ' menit',
                        'max_late_time' => $shift->max_late_time . ' menit',
                        'shift_start' => $shift->start_time,
                        'grace_period_end' => $gracePeriodEnd->format('H:i:s'),
                        'max_late_end' => $maxLateTime->format('H:i:s'),
                        'rfid_status' => $rfidCard->status,
                        'rfid_expired_date' => $expiredDate ? $expiredDate->format('Y-m-d') : null
                    ]
                ]
            ]);
        }

        // Jika sudah check in tapi belum check out
        if ($attendanceLog && !$attendanceLog->check_out_time) {
            // Hitung waktu minimal untuk check out (15 menit sebelum shift berakhir)
            $shiftEnd = Carbon::createFromFormat('Y-m-d H:i:s', $today->format('Y-m-d') . ' ' . $shift->end_time, 'Asia/Jakarta');
            $minCheckoutTime = $shiftEnd->copy()->subMinutes(15);

            if ($now->lt($minCheckoutTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya jam pulang',
                    'data' => [
                        'earliest_checkout' => $minCheckoutTime->format('H:i:s'),
                        'current_time' => $now->format('H:i:s'),
                        'shift_end' => $shift->end_time,
                    ]
                ], 400);
            }

            // Update check out time
            $workingHours = $this->calculateWorkingHours($attendanceLog->check_in_time, $now);
            $attendanceLog->update([
                'check_out_time' => $now,
                'working_hours' => $workingHours
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi pulang berhasil',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'nip' => $user->nip,
                        'division' => $user->division->name ?? '-',
                        'position' => $user->position->name ?? '-',
                        'photo_url' => $user->profile_photo_url,
                    ],
                    'attendance' => [
                        'check_in_time' => Carbon::parse($attendanceLog->check_in_time)->format('H:i:s'),
                        'check_out_time' => $now->format('H:i:s'),
                        'working_hours' => $workingHours,
                        'shift' => $shift->name,
                        'shift_end' => $shift->end_time,
                    ]
                ]
            ]);
        }

        // Jika sudah check in dan check out hari ini
        return response()->json([
            'success' => false,
            'message' => 'Anda sudah melakukan absensi masuk dan pulang hari ini',
        ], 400);
    }

    private function calculateWorkingHours($checkIn, $checkOut)
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        return round($checkOut->diffInMinutes($checkIn) / 60, 2);
    }
}
