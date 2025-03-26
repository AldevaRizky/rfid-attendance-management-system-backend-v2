<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'rfid_card_id', 'shift_id', 'check_in_time',
        'check_out_time', 'check_in_location', 'check_out_location',
        'status', 'date', 'working_hours', 'overtime_hours'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rfidCard()
    {
        return $this->belongsTo(RfidCard::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
